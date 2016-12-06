<?php
namespace Concrete\Core\Http\Client\Adapter;

use Zend\Http\Client\Adapter\Curl as ZendCurl;
use Concrete\Core\File\Exception\RequestTimeoutException;
use Zend\Http\Client\Adapter\Exception\TimeoutException as ZendTimeoutException;
use Zend\Http\Client\Adapter\Exception\RuntimeException as ZendRuntimeException;

class Curl extends ZendCurl
{
    /**
     * {@inheritdoc}
     *
     * @see ZendCurl::connect()
     */
    public function connect($host, $port = 80, $secure = false)
    {
        $timeut = isset($this->config['timeout']) ? $this->config['timeout'] : null;
        unset($this->config['timeout']);
        parent::connect($host, $port, $secure);
        if (isset($this->config['connecttimeout'])) {
            curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->config['connecttimeout']);
        }
        if (isset($this->config['executetimeout'])) {
            curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->config['executetimeout']);
        }
        if (isset($this->config['sslcafile'])) {
            curl_setopt($this->curl, CURLOPT_CAINFO, $this->config['sslcafile']);
        }
        if (isset($this->config['sslcapath'])) {
            curl_setopt($this->curl, CURLOPT_CAPATH, $this->config['sslcapath']);
        }
        $this->config['timeout'] = $timeut;
    }

    /**
     * {@inheritdoc}
     *
     * @see ZendCurl::write()
     */
    public function write($method, $uri, $httpVersion = 1.1, $headers = [], $body = '')
    {
        try {
            return parent::write($method, $uri, $httpVersion, $headers, $body);
        } catch (ZendTimeoutException $x) {
            throw new RequestTimeoutException(t('Request timed out.'));
        } catch (ZendRuntimeException $x) {
            if (@curl_errno($this->curl) === static::ERROR_OPERATION_TIMEDOUT) {
                throw new RequestTimeoutException(t('Request timed out.'));
            }
            throw $x;
        }
    }

    /**
     * No error.
     * All fine. Proceed as usual.
     *
     * @var int
     */
    const ERROR_OK = 0;

    /**
     * Unsupported protocol.
     * The URL you passed to libcurl used a protocol that this libcurl does not support.
     * The support might be a compile-time option that you didn't use, it can be a misspelled protocol string or just a protocol libcurl has no code for.
     *
     * @var int
     */
    const ERROR_UNSUPPORTED_PROTOCOL = 1;

    /**
     * Failed initialization.
     * Very early initialization code failed.
     * This is likely to be an internal error or problem, or a resource problem where something fundamental couldn't get done at init time.
     *
     * @var int
     */
    const ERROR_FAILED_INIT = 2;

    /**
     * URL using bad/illegal format or missing URL.
     * The URL was not properly formatted.
     *
     * @var int
     */
    const ERROR_URL_MALFORMAT = 3;

    /**
     * A requested feature, protocol or option was not found built-in in this libcurl due to a build-time decision.
     * This means that a feature or option was not enabled or explicitly disabled when libcurl was built and in order to get it to function you have to get a rebuilt libcurl.
     *
     * @var int
     */
    const ERROR_NOT_BUILT_IN = 4;

    /**
     * Couldn't resolve proxy name.
     * The given proxy host could not be resolved.
     *
     * @var int
     */
    const ERROR_COULDNT_RESOLVE_PROXY = 5;

    /**
     * Couldn't resolve host name.
     * The given remote host was not resolved.
     *
     * @var int
     */
    const ERROR_COULDNT_RESOLVE_HOST = 6;

    /**
     * Couldn't connect to server.
     * Failed to connect to host or proxy.
     *
     * @var int
     */
    const ERROR_COULDNT_CONNECT = 7;

    /**
     * Weird server reply.
     * The server sent data libcurl couldn't parse.
     *
     * @var int
     */
    const ERROR_WEIRD_SERVER_REPLY = 8;

    /**
     * Access denied to remote resource.
     * We were denied access to the resource given in the URL.
     * For FTP, this occurs while trying to change to the remote directory.
     *
     * @var int
     */
    const ERROR_REMOTE_ACCESS_DENIED = 9;

    /**
     * FTP: The server failed to connect to data port.
     * While waiting for the server to connect back when an active FTP session is used, an error code was sent over the control connection or similar.
     *
     * @var int
     */
    const ERROR_FTP_ACCEPT_FAILED = 10;

    /**
     * FTP: unknown PASS reply.
     * After having sent the FTP password to the server, libcurl expects a proper reply.
     * This error code indicates that an unexpected code was returned.
     *
     * @var int
     */
    const ERROR_FTP_WEIRD_PASS_REPLY = 11;

    /**
     * FTP: Accepting server connect has timed out.
     * During an active FTP session while waiting for the server to connect, the CURLOPT_ACCEPTTIMEOUT_MS (or the internal default) timeout expired.
     *
     * @var int
     */
    const ERROR_FTP_ACCEPT_TIMEOUT = 12;

    /**
     * FTP: unknown PASV reply.
     * libcurl failed to get a sensible result back from the server as a response to either a PASV or a EPSV command.
     * The server is flawed.
     *
     * @var int
     */
    const ERROR_FTP_WEIRD_PASV_REPLY = 13;

    /**
     * FTP: unknown 227 response format.
     * FTP servers return a 227-line as a response to a PASV command.
     * If libcurl fails to parse that line, this return code is passed back.
     *
     * @var int
     */
    const ERROR_FTP_WEIRD_227_FORMAT = 14;

    /**
     * FTP: can't figure out the host in the PASV response.
     * An internal failure to lookup the host used for the new connection.
     *
     * @var int
     */
    const ERROR_FTP_CANT_GET_HOST = 15;

    /**
     * Error in the HTTP2 framing layer.
     * This is somewhat generic and can be one out of several problems, see the error buffer for details.
     *
     * @var int
     */
    const ERROR_HTTP2 = 16;

    /**
     * FTP: couldn't set file type.
     * Received an error when trying to set the transfer mode to binary or ASCII.
     *
     * @var int
     */
    const ERROR_FTP_COULDNT_SET_TYPE = 17;

    /**
     * Transferred a partial file.
     * A file transfer was shorter or larger than expected.
     * This happens when the server first reports an expected transfer size, and then delivers data that doesn't match the previously given size.
     *
     * @var int
     */
    const ERROR_PARTIAL_FILE = 18;

    /**
     * FTP: couldn't retrieve (RETR failed) the specified file.
     * This was either a weird reply to a 'RETR' command or a zero byte transfer complete.
     *
     * @var int
     */
    const ERROR_FTP_COULDNT_RETR_FILE = 19;

    /**
     * Quote command returned error.
     * When sending custom "QUOTE" commands to the remote server, one of the commands returned an error code that was 400 or higher (for FTP) or otherwise indicated unsuccessful completion of the command.
     *
     * @var int
     */
    const ERROR_QUOTE_ERROR = 21;

    /**
     * HTTP response code said error.
     * This is returned if CURLOPT_FAILONERROR is set TRUE and the HTTP server returns an error code that is >= 400.
     *
     * @var int
     */
    const ERROR_HTTP_RETURNED_ERROR = 22;

    /**
     * Failed writing received data to disk/application.
     * An error occurred when writing received data to a local file, or an error was returned to libcurl from a write callback.
     *
     * @var int
     */
    const ERROR_WRITE_ERROR = 23;

    /**
     * Failed starting the upload.
     * For FTP, the server typically denied the STOR command.
     * The error buffer usually contains the server's explanation for this.
     *
     * @var int
     */
    const ERROR_UPLOAD_FAILED = 25;

    /**
     * Failed to open/read local data from file/application.
     * There was a problem reading a local file or an error returned by the read callback.
     *
     * @var int
     */
    const ERROR_READ_ERROR = 26;

    /**
     * Out of memory.
     * A memory allocation request failed.
     * This is serious badness and things are severely screwed up if this ever occurs.
     *
     * @var int
     */
    const ERROR_OUT_OF_MEMORY = 27;

    /**
     * Operation timeout.
     * The specified time-out period was reached according to the conditions.
     *
     * @var int
     */
    const ERROR_OPERATION_TIMEDOUT = 28;

    /**
     * FTP: command PORT failed.
     * This mostly happens when you haven't specified a good enough address for libcurl to use.
     * See CURLOPT_FTPPORT.
     *
     * @var int
     */
    const ERROR_FTP_PORT_FAILED = 30;

    /**
     * FTP: command REST failed.
     * This should never happen if the server is sane.
     *
     * @var int
     */
    const ERROR_FTP_COULDNT_USE_REST = 31;

    /**
     * Requested range was not delivered by the server.
     * The server does not support or accept range requests.
     *
     * @var int
     */
    const ERROR_RANGE_ERROR = 33;

    /**
     * Internal problem setting up the POST.
     * This is an odd error that mainly occurs due to internal confusion.
     *
     * @var int
     */
    const ERROR_HTTP_POST_ERROR = 34;

    /**
     * SSL connect error.
     * A problem occurred somewhere in the SSL/TLS handshake.
     * You really want the error buffer and read the message there as it pinpoints the problem slightly more.
     * Could be certificates (file formats, paths, permissions), passwords, and others.
     *
     * @var int
     */
    const ERROR_SSL_CONNECT_ERROR = 35;

    /**
     * Couldn't resume download.
     * The download could not be resumed because the specified offset was out of the file boundary.
     *
     * @var int
     */
    const ERROR_BAD_DOWNLOAD_RESUME = 36;

    /**
     * Couldn't read a file:// file.
     * Most likely because the file path doesn't identify an existing file. Did you check file permissions?
     *
     * @var int
     */
    const ERROR_FILE_COULDNT_READ_FILE = 37;

    /**
     * LDAP cannot bind.
     * LDAP bind operation failed.
     *
     * @var int
     */
    const ERROR_LDAP_CANNOT_BIND = 38;

    /**
     * LDAP search failed.
     *
     * @var int
     */
    const ERROR_LDAP_SEARCH_FAILED = 39;

    /**
     * A required function in the library was not found.
     *
     * @var int
     */
    const ERROR_FUNCTION_NOT_FOUND = 41;

    /**
     * Aborted by callback.
     * A callback returned "abort" to libcurl.
     *
     * @var int
     */
    const ERROR_ABORTED_BY_CALLBACK = 42;

    /**
     * Internal error.
     * A libcurl function was given a bad argument.
     *
     * @var int
     */
    const ERROR_BAD_FUNCTION_ARGUMENT = 43;

    /**
     * Failed binding local connection end.
     * A specified outgoing interface could not be used.
     * Set which interface to use for outgoing connections' source IP address with CURLOPT_INTERFACE.
     *
     * @var int
     */
    const ERROR_INTERFACE_FAILED = 45;

    /**
     * Too many redirects.
     * When following redirects, libcurl hit the maximum amount.
     * Set your limit with CURLOPT_MAXREDIRS.
     *
     * @var int
     */
    const ERROR_TOO_MANY_REDIRECTS = 47;

    /**
     * An option passed to libcurl is not recognized/known.
     * Refer to the appropriate documentation.
     * This is most likely a problem in the program that uses libcurl.
     * The error buffer might contain more specific information about which exact option it concerns.
     *
     * @var int
     */
    const ERROR_UNKNOWN_OPTION = 48;

    /**
     * Malformed telnet option.
     *
     * @var int
     */
    const ERROR_TELNET_OPTION_SYNTAX = 49;

    /**
     * SSL peer certificate or SSH remote key was not OK.
     * The remote server's SSL certificate or SSH md5 fingerprint was deemed not OK.
     *
     * @var int
     */
    const ERROR_PEER_FAILED_VERIFICATION = 51;

    /**
     * Server returned nothing (no headers, no data).
     * Nothing was returned from the server, and under the circumstances, getting nothing is considered an error.
     *
     * @var int
     */
    const ERROR_GOT_NOTHING = 52;

    /**
     * The specified crypto engine wasn't found.
     *
     * @var int
     */
    const ERROR_SSL_ENGINE_NOTFOUND = 53;

    /**
     * Can not set SSL crypto engine as default.
     *
     * @var int
     */
    const ERROR_SSL_ENGINE_SETFAILED = 54;

    /**
     * Failed sending data to the peer.
     *
     * @var int
     */
    const ERROR_SEND_ERROR = 55;

    /**
     * Failure when receiving data from the peer.
     *
     * @var int
     */
    const ERROR_RECV_ERROR = 56;

    /**
     * Problem with the local SSL certificate.
     *
     * @var int
     */
    const ERROR_SSL_CERTPROBLEM = 58;

    /**
     * Couldn't use specified SSL cipher.
     *
     * @var int
     */
    const ERROR_SSL_CIPHER = 59;

    /**
     * Peer certificate cannot be authenticated with given CA certificates.
     *
     * @var int
     */
    const ERROR_SSL_CACERT = 60;

    /**
     * Unrecognized or bad HTTP Content or Transfer-Encoding.
     *
     * @var int
     */
    const ERROR_BAD_CONTENT_ENCODING = 61;

    /**
     * Invalid LDAP URL.
     *
     * @var int
     */
    const ERROR_LDAP_INVALID_URL = 62;

    /**
     * Maximum file size exceeded.
     *
     * @var int
     */
    const ERROR_FILESIZE_EXCEEDED = 63;

    /**
     * Requested SSL level failed.
     *
     * @var int
     */
    const ERROR_USE_SSL_FAILED = 64;

    /**
     * Send failed since rewinding of the data stream failed.
     * When doing a send operation curl had to rewind the data to retransmit, but the rewinding operation failed.
     *
     * @var int
     */
    const ERROR_SEND_FAIL_REWIND = 65;

    /**
     * Failed to initialise SSL crypto engine.
     *
     * @var int
     */
    const ERROR_SSL_ENGINE_INITFAILED = 66;

    /**
     * The remote server denied login.
     *
     * @var int
     */
    const ERROR_LOGIN_DENIED = 67;

    /**
     * File not found on TFTP server.
     *
     * @var int
     */
    const ERROR_TFTP_NOTFOUND = 68;

    /**
     * Permission problem on TFTP server.
     *
     * @var int
     */
    const ERROR_TFTP_PERM = 69;

    /**
     * Out of disk space on the server.
     *
     * @var int
     */
    const ERROR_REMOTE_DISK_FULL = 70;

    /**
     * Illegal TFTP operation.
     *
     * @var int
     */
    const ERROR_TFTP_ILLEGAL = 71;

    /**
     * Unknown TFTP transfer ID.
     *
     * @var int
     */
    const ERROR_TFTP_UNKNOWNID = 72;

    /**
     * Remote file already exists and will not be overwritten.
     *
     * @var int
     */
    const ERROR_REMOTE_FILE_EXISTS = 73;

    /**
     * TFTP: No such user.
     *
     * @var int
     */
    const ERROR_TFTP_NOSUCHUSER = 74;

    /**
     * Character conversion failed.
     *
     * @var int
     */
    const ERROR_CONV_FAILED = 75;

    /**
     * Caller must register CURLOPT_CONV_ callback options.
     *
     * @var int
     */
    const ERROR_CONV_REQD = 76;

    /**
     * Problem with the SSL CA cert (path? access rights?).
     *
     * @var int
     */
    const ERROR_SSL_CACERT_BADFILE = 77;

    /**
     * Remote file not found.
     *
     * @var int
     */
    const ERROR_REMOTE_FILE_NOT_FOUND = 78;

    /**
     * An unspecified error occurred during the SSH session.
     *
     * @var int
     */
    const ERROR_SSH = 79;

    /**
     * Failed to shut down the SSL connection.
     *
     * @var int
     */
    const ERROR_SSL_SHUTDOWN_FAILED = 80;

    /**
     * Socket is not ready for send/recv.
     * Wait till it's ready and try again.
     * This return code is only returned from curl_easy_recv and curl_easy_send.
     *
     * (Added in 7.18.2)
     *
     * @var int
     */
    const ERROR_AGAIN = 81;

    /**
     * Failed to load CRL file (path? access rights?, format?).
     *
     * (Added in 7.19.0)
     *
     * @var int
     */
    const ERROR_SSL_CRL_BADFILE = 82;

    /**
     * Issuer check against peer certificate failed.
     *
     * (Added in 7.19.0)
     *
     * @var int
     */
    const ERROR_SSL_ISSUER_ERROR = 83;

    /**
     * FTP: The server did not accept the PRET command.
     * The FTP server does not understand the PRET command at all or does not support the given argument.
     * Be careful when using CURLOPT_CUSTOMREQUEST, a custom LIST command will be sent with PRET CMD before PASV as well.
     *
     * (Added in 7.20.0).
     *
     * @var int
     */
    const ERROR_FTP_PRET_FAILED = 84;

    /**
     * RTSP CSeq mismatch or invalid CSeq.
     *
     * @var int
     */
    const ERROR_RTSP_CSEQ_ERROR = 85;

    /**
     * Mismatch of RTSP Session Identifiers.
     *
     * @var int
     */
    const ERROR_RTSP_SESSION_ERROR = 86;

    /**
     * Unable to parse FTP file list.
     * This may happen during FTP wildcard downloading.
     *
     * @var int
     */
    const ERROR_FTP_BAD_FILE_LIST = 87;

    /**
     * Chunk callback reported error.
     *
     * @var int
     */
    const ERROR_CHUNK_FAILED = 88;

    /**
     * The max connection limit is reached.
     * No connection available, the session will be queued.
     *
     * (For internal use only, will never be returned by libcurl)
     *
     * (Added in 7.30.0).
     *
     * @var int
     */
    const ERROR_NO_CONNECTION_AVAILABLE = 89;

    /**
     * SSL public key does not match pinned public key.
     * Failed to match the pinned key specified with CURLOPT_PINNEDPUBLICKEY.
     *
     * @var int
     */
    const ERROR_SSL_PINNEDPUBKEYNOTMATCH = 90;

    /**
     * SSL server certificate status verification FAILED.
     * Status returned failure when asked with CURLOPT_SSL_VERIFYSTATUS.
     *
     * @var int
     */
    const ERROR_SSL_INVALIDCERTSTATUS = 91;

    /**
     * Stream error in the HTTP/2 framing layer.
     *
     * @var int
     */
    const ERROR_HTTP2_STREAM = 92;
}
