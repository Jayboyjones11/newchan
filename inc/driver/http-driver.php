<?php // Honestly this is just a wrapper for cURL. Still useful to mock it and have an OOP API on PHP 7.
namespace Vichan\Driver;

use RuntimeException;

defined('TINYBOARD') or exit;


class HttpDrivers {
	private const DEFAULT_USER_AGENT = 'Tinyboard';


	public static function getHttpDriver(int $timeout, int $max_file_size): HttpDriver {
		return new HttpDriver($timeout, self::DEFAULT_USER_AGENT, $max_file_size);
	}
}

class HttpDriver {
	private mixed $inner;
	private int $timeout;
	private string $user_agent;
	private int $max_file_size;


	private function resetTowards(string $url, int $timeout): void {
		curl_reset($this->inner);
		curl_setopt_array($this->inner, array(
			CURLOPT_URL => $url,
			CURLOPT_TIMEOUT => $this->timeout,
			CURLOPT_USERAGENT => $this->user_agent,
			CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
		));
	}

	private function setSizeLimit(): void {
		// Adapted from: https://stackoverflow.com/a/17642638
		curl_setopt($this->inner, CURLOPT_NOPROGRESS, false);

		if (PHP_MAJOR_VERSION >= 8 && PHP_MINOR_VERSION >= 2) {
			curl_setopt($this->inner, CURLOPT_XFERINFOFUNCTION, function($res, $next_dl, $dl, $next_up, $up) {
				return (int)($dl <= $this->max_file_size);
			});
		} else {
			curl_setopt($this->inner, CURLOPT_PROGRESSFUNCTION, function($res, $next_dl, $dl, $next_up, $up) {
				return (int)($dl <= $this->max_file_size);
			});
		}
	}

	function __construct($timeout, $user_agent, $max_file_size) {
		$this->inner = curl_init();
		$this->timeout = $timeout;
		$this->user_agent = $user_agent;
		$this->max_file_size = $max_file_size;
	}

	function __destruct() {
		curl_close($this->inner);
	}

	/**
	 * Execute a GET request.
	 *
	 * @param string $endpoint Uri endpoint.
	 * @param ?array $data Optional GET parameters.
	 * @param int $timeout Optional request timeout in seconds. Use the default timeout if 0.
	 * @return string Returns the body of the response.
	 * @throws RuntimeException Throws on IO error.
	 */
	public function requestGet(string $endpoint, ?array $data, int $timeout = 0): string {
		if (!empty($data)) {
			$endpoint .= '?' . http_build_query($data);
		}
		if ($timeout == 0) {
			$timeout = $this->timeout;
		}

		$this->resetTowards($endpoint, $timeout);
		curl_setopt($this->inner, CURLOPT_RETURNTRANSFER, true);
		$ret = curl_exec($this->inner);

		if ($ret === false) {
			throw new \RuntimeException(curl_error($this->inner));
		}
		return $ret;
	}

	/**
	 * Execute a POST request.
	 *
	 * @param string $endpoint Uri endpoint.
	 * @param ?array $data Optional POST parameters.
	 * @param int $timeout Optional request timeout in seconds. Use the default timeout if 0.
	 * @return string Returns the body of the response.
	 * @throws RuntimeException Throws on IO error.
	 */
	public function requestPost(string $endpoint, ?array $data, int $timeout = 0): string {
		if ($timeout == 0) {
			$timeout = $this->timeout;
		}

		$this->resetTowards($endpoint, $timeout);
		curl_setopt($this->inner, CURLOPT_POST, true);
		if (!empty($data)) {
			curl_setopt($this->inner, CURLOPT_POSTFIELDS, http_build_query($data));
		}
		curl_setopt($this->inner, CURLOPT_RETURNTRANSFER, true);
		$ret = curl_exec($this->inner);

		if ($ret === false) {
			throw new \RuntimeException(curl_error($this->inner));
		}
		return $ret;
	}

	/**
	 * Download the url's target with curl.
	 *
	 * @param string $url Url to the file to download.
	 * @param ?array $data Optional GET parameters.
	 * @param resource $fd File descriptor to save the content to.
	 * @param int $timeout Optional request timeout in seconds. Use the default timeout if 0.
	 * @return bool Returns true on success, false if the file was too large.
	 * @throws RuntimeException Throws on IO error.
	 */
	public function requestGetInto(string $endpoint, ?array $data, mixed $fd, int $timeout = 0): bool {
		if (!empty($data)) {
			$endpoint .= '?' . http_build_query($data);
		}
		if ($timeout == 0) {
			$timeout = $this->timeout;
		}

		$this->resetTowards($endpoint, $timeout);
		curl_setopt($this->inner, CURLOPT_FAILONERROR, true);
		curl_setopt($this->inner, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($this->inner, CURLOPT_FILE, $fd);
		curl_setopt($this->inner, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		$this->setSizeLimit();
		$ret = curl_exec($this->inner);

		if ($ret === false) {
			if (curl_errno($this->inner) === CURLE_ABORTED_BY_CALLBACK) {
				return false;
			}

			throw new \RuntimeException(curl_error($this->inner));
		}
		return true;
	}
}
