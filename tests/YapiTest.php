<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Yapi\Yapi;
use Yapi\Core\Response;

final class YapiTest extends TestCase
{
	public function testCanInitializeResponseObject(): void {
		$yapi = new Yapi();
		$this->assertInstanceOf(
			Response::class,
			$yapi->initResponse()
		);
	}
	// public function testCanBeCreatedFromValidEmailAddress(): void
	// {
	// 	$this->assertInstanceOf(
	// 		Email::class,
	// 		Email::fromString('user@example.com')
	// 	);
	// }

	// public function testCannotBeCreatedFromInvalidEmailAddress(): void
	// {
	// 	$this->expectException(InvalidArgumentException::class);

	// 	Email::fromString('invalid');
	// }

	// public function testCanBeUsedAsString(): void
	// {
	// 	$this->assertEquals(
	// 		'user@example.com',
	// 		Email::fromString('user@example.com')
	// 	);
	// }
}
