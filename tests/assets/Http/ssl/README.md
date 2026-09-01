# Fake certificates for the test suite

These files are used by the fake HTTPS server started by the test suite (see `tests/assets/Http/fake-https-server.php`
and `tests/helpers/Http/FakeHttpsServer.php`).

**They are fake certificates, to be used only by the tests: never use them anywhere else.**

- `ca.crt`: the (fake) certification authority that signed the certificate of the server. Test clients that should
  consider the fake server as trusted must use this file as their CA bundle.
- `server.crt` / `server.key`: the certificate (and its private key) used by the fake HTTPS server. It's valid for the
  `localhost` host name, as well as for the `127.0.0.1` and `::1` IP addresses.

## Regenerating the certificates

They expire in 2046. In order to recreate them, run these commands from within this directory:

```sh
openssl req -x509 -nodes -newkey rsa:2048 -sha256 -days 7300 \
  -keyout ca.key -out ca.crt \
  -subj "/C=US/O=Concrete CMS test suite/CN=Concrete CMS test suite fake CA" \
  -addext "basicConstraints=critical,CA:TRUE,pathlen:0" \
  -addext "keyUsage=critical,keyCertSign,cRLSign"

openssl req -nodes -newkey rsa:2048 -sha256 \
  -keyout server.key -out server.csr \
  -subj "/C=US/O=Concrete CMS test suite/CN=localhost" \
  -addext "subjectAltName=DNS:localhost,IP:127.0.0.1,IP:::1" \
  -addext "basicConstraints=critical,CA:FALSE" \
  -addext "keyUsage=critical,digitalSignature,keyEncipherment" \
  -addext "extendedKeyUsage=serverAuth"

openssl x509 -req -in server.csr -CA ca.crt -CAkey ca.key -CAcreateserial \
  -days 7300 -sha256 -copy_extensions=copyall -out server.crt

rm -f server.csr ca.srl ca.key
```

The private key of the certification authority (`ca.key`) is not kept: if you need to sign a new server certificate,
simply recreate the whole chain with the commands above.
