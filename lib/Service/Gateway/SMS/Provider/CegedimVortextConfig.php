<?php

declare(strict_types=1);

/**
 * @author Pierre LEROUGE <pierre.lerouge@cegedim.com>
 *
 * Nextcloud - Two-factor Gateway for Vortext
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactorGateway\Service\Gateway\SMS\Provider;

use function array_intersect;
use OCA\TwoFactorGateway\AppInfo\Application;
use OCA\TwoFactorGateway\Exception\ConfigurationException;
use OCP\IConfig;

class CegedimVortextConfig implements IProviderConfig {

	const expected = [
		'cegedimvortext_username',
		'cegedimvortext_password',
		'cegedimvortext_endpoint'
	];

	/** @var IConfig */
	private $config;

	public function __construct(IConfig $config) {
		$this->config = $config;
	}

	private function getOrFail(string $key): string {
		$val = $this->config->getAppValue(Application::APP_ID, $key, null);
		if (is_null($val)) {
			throw new ConfigurationException();
		}
		return $val;
	}

	public function getUsername(): string {
		return $this->getOrFail('cegedimvortext_username');
	}

	public function getPassword(): string {
		return $this->getOrFail('cegedimvortext_password');
	}

	public function getEndpoint(): string {
		return $this->getOrFail('cegedimvortext_endpoint');
	}

	public function setUsername(string $appKey) {
		$this->config->setAppValue(Application::APP_ID, 'cegedimvortext_username', $appKey);
	}

	public function setPassword(string $appSecret) {
		$this->config->setAppValue(Application::APP_ID, 'cegedimvortext_password', $appSecret);
	}

	public function setEndpoint(string $endpoint) {
		$this->config->setAppValue(Application::APP_ID, 'cegedimvortext_endpoint', $endpoint);
	}

	public function isComplete(): bool {
		$set = $this->config->getAppKeys(Application::APP_ID);
		return count(array_intersect($set, self::expected)) === count(self::expected);
	}

	public function remove() {
		foreach(self::expected as $key) {
			$this->config->deleteAppValue(Application::APP_ID, $key);
		}
	}
}