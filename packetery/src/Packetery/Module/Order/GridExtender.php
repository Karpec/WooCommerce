<?php
/**
 * Class GridExtender.
 *
 * @package Packetery\Order
 */

declare( strict_types=1 );

namespace Packetery\Module\Order;

use Packetery\Core\Helper;
use Packetery\Module\Carrier;
use PacketeryLatte\Engine;
use PacketeryNette\Http\Request;

/**
 * Class GridExtender.
 *
 * @package Packetery\Order
 */
class GridExtender {
	/**
	 * Generic Helper.
	 *
	 * @var Helper
	 */
	private $helper;

	/**
	 * Carrier repository.
	 *
	 * @var Carrier\Repository
	 */
	private $carrierRepository;

	/**
	 * Latte Engine.
	 *
	 * @var Engine
	 */
	private $latteEngine;

	/**
	 * Http Request.
	 *
	 * @var Request
	 */
	private $httpRequest;

	/**
	 * Controller router.
	 *
	 * @var ControllerRouter
	 */
	private $orderControllerRouter;

	/**
	 * Order repository.
	 *
	 * @var Repository
	 */
	private $orderRepository;

	/**
	 * GridExtender constructor.
	 *
	 * @param Helper             $helper                Helper.
	 * @param Carrier\Repository $carrierRepository     Carrier repository.
	 * @param Engine             $latteEngine           Latte Engine.
	 * @param Request            $httpRequest           Http Request.
	 * @param ControllerRouter   $orderControllerRouter Order controller router.
	 * @param Repository         $orderRepository       Order repository.
	 */
	public function __construct(
		Helper $helper,
		Carrier\Repository $carrierRepository,
		Engine $latteEngine,
		Request $httpRequest,
		ControllerRouter $orderControllerRouter,
		Repository $orderRepository
	) {
		$this->helper                = $helper;
		$this->carrierRepository     = $carrierRepository;
		$this->latteEngine           = $latteEngine;
		$this->httpRequest           = $httpRequest;
		$this->orderControllerRouter = $orderControllerRouter;
		$this->orderRepository       = $orderRepository;
	}

	/**
	 * Adds custom filtering links to order grid.
	 *
	 * @param array $var Array of html links.
	 *
	 * @return array
	 */
	public function addFilterLinks( array $var ): array {
		$latteParams = [
			'link'       => add_query_arg(
				[
					'post_type'           => 'shop_order',
					'filter_action'       => 'packetery_filter_link',
					'packetery_to_submit' => '1',
					'packetery_to_print'  => false,
				],
				admin_url( 'edit.php' )
			),
			'title'      => __( 'packetaOrdersToSubmit', 'packetery' ),
			'orderCount' => $this->orderRepository->countOrdersToSubmit(),
			'active'     => ( $this->httpRequest->getQuery( 'packetery_to_submit' ) === '1' ),
		];
		$var[]       = $this->latteEngine->renderToString( PACKETERY_PLUGIN_DIR . '/template/order/filter-link.latte', $latteParams );

		$latteParams = [
			'link'       => add_query_arg(
				[
					'post_type'           => 'shop_order',
					'filter_action'       => 'packetery_filter_link',
					'packetery_to_submit' => false,
					'packetery_to_print'  => '1',
				],
				admin_url( 'edit.php' )
			),
			'title'      => __( 'packetaOrdersToPrint', 'packetery' ),
			'orderCount' => $this->orderRepository->countOrdersToPrint(),
			'active'     => ( $this->httpRequest->getQuery( 'packetery_to_print' ) === '1' ),
		];
		$var[]       = $this->latteEngine->renderToString( PACKETERY_PLUGIN_DIR . '/template/order/filter-link.latte', $latteParams );

		return $var;
	}

	/**
	 * Adds select to order grid.
	 */
	public function renderOrderTypeSelect(): void {
		$linkFilters = [];

		if ( null !== $this->httpRequest->getQuery( 'packetery_to_submit' ) ) {
			$linkFilters['packetery_to_submit'] = '1';
		}

		if ( null !== $this->httpRequest->getQuery( 'packetery_to_print' ) ) {
			$linkFilters['packetery_to_print'] = '1';
		}

		$this->latteEngine->render(
			PACKETERY_PLUGIN_DIR . '/template/order/type-select.latte',
			[
				'packeteryOrderType' => $this->httpRequest->getQuery( 'packetery_order_type' ),
				'linkFilters'        => $linkFilters,
			]
		);
	}

	/**
	 * Fills custom order list columns.
	 *
	 * @param string $column Current order column name.
	 */
	public function fillCustomOrderListColumns( string $column ): void {
		global $post;
		$wcOrder = wc_get_order( $post->ID );
		$order   = $this->orderRepository->getByWcOrder( $wcOrder );
		if ( null === $order ) {
			return;
		}

		switch ( $column ) {
			case 'packetery_destination':
				$pickupPoint = $order->getPickupPoint();
				if ( null !== $pickupPoint ) {
					$pointName         = $pickupPoint->getName();
					$pointId           = $pickupPoint->getId();
					$country           = strtolower( $wcOrder->get_shipping_country() );
					$internalCountries = array_keys( $this->carrierRepository->getZpointCarriers() );
					if ( in_array( $country, $internalCountries, true ) ) {
						echo esc_html( "$pointName ($pointId)" );
					} else {
						echo esc_html( $pointName );
					}
					break;
				}
				$homeDeliveryCarrier = $this->carrierRepository->getById( (int) $order->getCarrierId() );
				if ( $homeDeliveryCarrier ) {
					$homeDeliveryCarrierEntity = new Carrier\Entity( $homeDeliveryCarrier );
					echo esc_html( $homeDeliveryCarrierEntity->getFinalName() );
				}
				break;
			case 'packetery_packet_id':
				$packetId = $order->getPacketId();
				if ( $packetId ) {
					echo '<a href="' . esc_attr( $this->helper->get_tracking_url( $packetId ) ) . '" target="_blank">Z' . esc_html( $packetId ) . '</a>';
				}
				break;
			case 'packetery':
				$packetSubmitUrl = add_query_arg( [], $this->orderControllerRouter->getRouteUrl( Controller::PATH_SUBMIT_TO_API ) );
				$printLink       = add_query_arg(
					[
						'page'                       => LabelPrint::MENU_SLUG,
						LabelPrint::LABEL_TYPE_PARAM => ( $order->isExternalCarrier() ? LabelPrint::ACTION_CARRIER_LABELS : LabelPrint::ACTION_PACKETA_LABELS ),
						'id'                         => $order->getNumber(),
						'packet_id'                  => $order->getPacketId(),
						'offset'                     => 0,
					],
					admin_url( 'admin.php' )
				);
				$this->latteEngine->render(
					PACKETERY_PLUGIN_DIR . '/template/order/grid-column-packetery.latte',
					[
						'order'           => $order,
						'hasOrderWeight'  => ( null !== $order->getWeight() && $order->getWeight() > 0 ),
						'packetSubmitUrl' => $packetSubmitUrl,
						'restNonce'       => wp_create_nonce( 'wp_rest' ),
						'printLink'       => $printLink,
					]
				);
				break;
			case 'packetery_packet_status':
				echo esc_html( $this->getPacketStatusTranslated( $order->getPacketStatus() ) );
				break;
		}
	}

	/**
	 * Gets code text translated.
	 *
	 * @param string|null $packetStatus Packet status.
	 *
	 * @return string|null
	 */
	public function getPacketStatusTranslated( ?string $packetStatus ): string {
		switch ( $packetStatus ) {
			case 'received data':
				return __( 'packetStatusReceivedData', 'packetery' );
			case 'arrived':
				return __( 'packetStatusArrived', 'packetery' );
			case 'prepared for departure':
				return __( 'packetStatusPreparedForDeparture', 'packetery' );
			case 'departed':
				return __( 'packetStatusDeparted', 'packetery' );
			case 'ready for pickup':
				return __( 'packetStatusReadyForPickup', 'packetery' );
			case 'handed to carrier':
				return __( 'packetStatusHandedToCarrier', 'packetery' );
			case 'delivered':
				return __( 'packetStatusDelivered', 'packetery' );
			case 'posted back':
				return __( 'packetStatusPostedBack', 'packetery' );
			case 'returned':
				return __( 'packetStatusReturned', 'packetery' );
			case 'cancelled':
				return __( 'packetStatusCancelled', 'packetery' );
			case 'collected':
				return __( 'packetStatusCollected', 'packetery' );
			case 'unknown':
				return __( 'packetStatusUnknown', 'packetery' );
		}

		return (string) $packetStatus;
	}

	/**
	 * Add order list columns.
	 *
	 * @param string[] $columns Order list columns.
	 *
	 * @return string[] All columns.
	 */
	public function addOrderListColumns( array $columns ): array {
		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {
			$new_columns[ $column_name ] = $column_info;

			if ( 'order_total' === $column_name ) {
				$new_columns['packetery_packet_status'] = __( 'packetaPacketStatus', 'packetery' );
				$new_columns['packetery']               = __( 'Packeta', 'packetery' );
				$new_columns['packetery_packet_id']     = __( 'Barcode', 'packetery' );
				$new_columns['packetery_destination']   = __( 'Pick up point or carrier', 'packetery' );
			}
		}

		return $new_columns;
	}
}
