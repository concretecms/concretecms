<?php

declare(strict_types=1);

/**
 * A minimal HTTPS server used by the test suite, so that we don't need to perform requests to live websites.
 *
 * This is a standalone script (it doesn't bootstrap Concrete): it's started by
 * \Concrete\TestHelpers\Http\FakeHttpsServer, which reads from our standard output the port we listen to.
 *
 * Usage: php fake-https-server.php <certificate-file> <private-key-file> <keepalive-file>
 *
 * We listen to a port assigned by the operating system (so that we never use a port already in use), and we print it
 * to the standard output as a "PORT:<port>" line (in case of errors we print an "ERROR:<message>" line instead).
 *
 * We quit as soon as the keepalive file is deleted (that's what the process that started us does when it ends), or
 * after MAX_LIFETIME seconds (so that we never stay around forever if that process dies abruptly).
 *
 * @php-cs-fixer-ignore ConcreteCMS/ensure_defined_or_die
 */

/**
 * The number of seconds we wait for new connections before checking again if we should quit.
 *
 * @var float
 */
const SOCKET_ACCEPT_TIMEOUT = 0.5;

/**
 * The number of seconds we wait for the data sent by the clients.
 *
 * @var int
 */
const CLIENT_TIMEOUT = 5;

/**
 * The maximum size (in bytes) of the requests we accept.
 *
 * @var int
 */
const MAX_REQUEST_LENGTH = 65536;

/**
 * The maximum number of seconds we stay alive.
 *
 * @var int
 */
const MAX_LIFETIME = 3600;

/**
 * Print an error line to the standard output, and quit with an error code.
 */
function fail(string $message): void
{
    fwrite(STDOUT, "ERROR:{$message}\n");
    fflush(STDOUT);
    exit(1);
}

/**
 * Create the listening socket, binding it to a port chosen by the operating system.
 *
 * @return resource
 */
function createServerSocket(string $certificateFile, string $privateKeyFile)
{
    $context = stream_context_create([
        'ssl' => [
            'local_cert' => $certificateFile,
            'local_pk' => $privateKeyFile,
            'allow_self_signed' => true,
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ]);
    $errorCode = 0;
    $errorMessage = '';
    // We listen to a plain TCP socket and we start the TLS negotiation after accepting the connections: this way a
    // failed TLS handshake (which is exactly what some tests check) doesn't affect the other connections.
    $socket = @stream_socket_server('tcp://127.0.0.1:0', $errorCode, $errorMessage, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
    if ($socket === false) {
        fail($errorMessage === '' ? 'Failed to create the listening socket' : $errorMessage);
    }

    return $socket;
}

/**
 * Read the HTTP request sent by a client.
 *
 * @param resource $client
 *
 * @return string[]|null the request line and the request headers (NULL if the request is malformed/incomplete)
 */
function readRequest($client): ?array
{
    $data = '';
    while (strpos($data, "\r\n\r\n") === false) {
        $chunk = @fread($client, 4096);
        if ($chunk === false || $chunk === '') {
            return null;
        }
        $data .= $chunk;
        if (strlen($data) > MAX_REQUEST_LENGTH) {
            return null;
        }
        $info = stream_get_meta_data($client);
        if (!empty($info['timed_out'])) {
            return null;
        }
    }

    return explode("\r\n", substr($data, 0, strpos($data, "\r\n\r\n")));
}

/**
 * Send the response to a client.
 *
 * @param resource $client
 */
function sendResponse($client, string $requestLine): void
{
    $method = strtoupper((string) strtok($requestLine, ' '));
    $body = "Hello from the Concrete CMS fake HTTPS server.\n";
    $headers = [
        'HTTP/1.1 200 OK',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Length: ' . strlen($body),
        'X-Fake-Https-Server: 1',
        'Connection: close',
    ];
    // Responses to HEAD requests must not contain a body
    @fwrite($client, implode("\r\n", $headers) . "\r\n\r\n" . ($method === 'HEAD' ? '' : $body));
}

/**
 * Serve a single client connection.
 *
 * @param resource $client
 */
function serveClient($client): void
{
    try {
        stream_set_blocking($client, true);
        stream_set_timeout($client, CLIENT_TIMEOUT);
        if (@stream_socket_enable_crypto($client, true, STREAM_CRYPTO_METHOD_TLS_SERVER) !== true) {
            // The client refused our (fake) certificate: that's fine, it may be exactly what a test is checking.
            return;
        }
        $request = readRequest($client);
        if ($request !== null) {
            sendResponse($client, $request[0]);
        }
    } finally {
        @fclose($client);
    }
}

$certificateFile = $argv[1] ?? '';
$privateKeyFile = $argv[2] ?? '';
$keepaliveFile = $argv[3] ?? '';
if (!is_file($certificateFile) || !is_file($privateKeyFile) || $keepaliveFile === '') {
    fail('Usage: php ' . basename(__FILE__) . ' <certificate-file> <private-key-file> <keepalive-file>');
}
if (!extension_loaded('openssl')) {
    fail('The openssl PHP extension is not available');
}

$server = createServerSocket($certificateFile, $privateKeyFile);
$address = (string) stream_socket_get_name($server, false);
$port = (int) substr($address, (int) strrpos($address, ':') + 1);
if ($port < 1) {
    fail('Failed to determine the listening port');
}
fwrite(STDOUT, "PORT:{$port}\n");
fflush(STDOUT);

$quitAt = time() + MAX_LIFETIME;
while (time() < $quitAt) {
    clearstatcache(true, $keepaliveFile);
    if (!is_file($keepaliveFile)) {
        // The process that started us is gone: let's quit too.
        break;
    }
    $read = [$server];
    $write = null;
    $except = null;
    $ready = @stream_select($read, $write, $except, 0, (int) (SOCKET_ACCEPT_TIMEOUT * 1000000));
    if ($ready === false) {
        break;
    }
    if ($ready > 0) {
        $client = @stream_socket_accept($server, SOCKET_ACCEPT_TIMEOUT);
        if ($client !== false) {
            serveClient($client);
        }
    }
}

fclose($server);
exit(0);
