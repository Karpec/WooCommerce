<?php

declare( strict_types=1 );

namespace Tests\Module\Options;

use Packetery\Core\Entity\PacketStatus;
use Packetery\Module\Framework\WpAdapter;
use Packetery\Module\Options\OptionsProvider;
use PHPUnit\Framework\TestCase;

class OptionsProviderTest extends TestCase {
	public static function dimensionsUnitProvider(): array {
		return [
			[
				'unit'             => 'cm',
				'expectedDecimals' => 1,
			],
			[
				'unit'             => 'mm',
				'expectedDecimals' => 0,
			],
			[
				'unit'             => 'in',
				'expectedDecimals' => 0,
			],
			[
				'unit'             => 'l',
				'expectedDecimals' => 0,
			],
		];
	}

	/**
	 * @dataProvider dimensionsUnitProvider
	 */
	public function testGetDimensionsNumberOfDecimals( string $unit, int $expectedDecimals ): void {
		$provider = $this->getMockBuilder( OptionsProvider::class )
			->disableOriginalConstructor()
			->onlyMethods( [ 'getDimensionsUnit' ] )
			->getMock();

		$provider->method( 'getDimensionsUnit' )
			->willReturn( $unit );

		$result = $provider->getDimensionsNumberOfDecimals();
		$this->assertSame( $expectedDecimals, $result );
	}

	public static function sanitiseDimensionProvider(): array {
		return [
			[
				'dimensionValue'   => '',
				'expectedValue'    => null,
				'numberOfDecimals' => 1,
				'unit'             => 'cm',
			],
			[
				'dimensionValue'   => 23.3567,
				'expectedValue'    => 234,
				'numberOfDecimals' => 1,
				'unit'             => 'cm',
			],
			[
				'dimensionValue'   => 10.0,
				'expectedValue'    => 100,
				'numberOfDecimals' => 1,
				'unit'             => 'cm',
			],
			[
				'dimensionValue'   => 0.100000000,
				'expectedValue'    => 1,
				'numberOfDecimals' => 1,
				'unit'             => 'cm',
			],
			[
				'dimensionValue'   => 200,
				'expectedValue'    => 200,
				'numberOfDecimals' => 0,
				'unit'             => 'mm',
			],
			[
				'dimensionValue'   => 200,
				'expectedValue'    => 200,
				'numberOfDecimals' => 0,
				'unit'             => 'mm',
			],
		];
	}

	/**
	 * @dataProvider sanitiseDimensionProvider
	 */
	public function testGetSanitizedDimensionValueInMm(
		float|int|string $dimensionValue,
		?int $expectedValue,
		int $numberOfDecimals,
		string $unit
	): void {
		$provider = $this->getMockBuilder( OptionsProvider::class )
			->disableOriginalConstructor()
			->onlyMethods( [ 'getDimensionsNumberOfDecimals', 'getDimensionsUnit' ] )
			->getMock();

		$provider->method( 'getDimensionsNumberOfDecimals' )
			->willReturn( $numberOfDecimals );

		$provider->method( 'getDimensionsUnit' )
			->willReturn( $unit );

		$result = $provider->getSanitizedDimensionValueInMm( $dimensionValue );
		$this->assertEquals( $expectedValue, $result );
	}

	public function testStatusSyncingPacketStatuses(): void {
		$wpAdapterMock = $this->createMock( WpAdapter::class );
		$wpAdapterMock
			->method( 'getOption' )
			->willReturnCallback(
				static function ( string $key ): array {
					if ( $key === OptionsProvider::OPTION_NAME_PACKETERY_SYNC ) {
						return [ 'status_syncing_packet_statuses' => [ PacketStatus::DEPARTED, PacketStatus::UNKNOWN ] ];
					}

					return [];
				}
			);

		$provider = new OptionsProvider(
			$wpAdapterMock
		);

		$result = $provider->getStatusSyncingPacketStatuses(
			[
				new PacketStatus( PacketStatus::DEPARTED, PacketStatus::DEPARTED, true ),
				new PacketStatus( PacketStatus::ARRIVED, PacketStatus::ARRIVED, true ),
			]
		);
		$this->assertSame( [ PacketStatus::DEPARTED ], $result );
	}
}
