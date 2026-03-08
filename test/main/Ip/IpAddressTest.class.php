<?php

	final class IpAddressToSignedIntTest extends TestCase
	{
		private $ips =
			[
				['0.0.0.0',0],
				['0.0.0.1', 1],
				['64.64.64.64',1077952576],
				['127.255.255.254', 2147483646],
				['127.255.255.255', 2147483647],
				['128.0.0.0', -2147483648],
				['128.0.0.1', -2147483647],
				['192.192.192.192', -1061109568],
				['254.255.255.255', -16777217],
				['255.255.255.254', -2],
				['255.255.255.255', -1]
			];

		public function testIpToSignedInt()
		{
			foreach ($this->ips as $ip) {
				$this->assertEquals(
					IpAddress::create($ip[0])->toSignedInt(),
					$ip[1]
				);
			}
		}

		public function testIpToLongToIp()
		{
			foreach ($this->ips as $ip) {
				$this->assertEquals(
					long2ip(
						IpAddress::create($ip[0])->toSignedInt()
					),
					$ip[0]
				);
			}
		}

		public function testSignedLongToIpToSignedLong()
		{
			foreach ($this->ips as $ip) {
				$this->assertEquals(
					IpAddress::create(
						long2ip($ip[1])
					)->toSignedInt(),
					$ip[1]
				);
			}
		}
    }
