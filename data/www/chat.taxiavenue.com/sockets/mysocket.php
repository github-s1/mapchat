  <?php 
  $address = "127.0.0.1";
  $port = 4646;
   
  $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
   
  if( !$socket ) exit( socket_strerror( socket_last_error() ) );
  else echo 'Socket_created!'."\r\n";
   
  if( !socket_bind($socket, $address, $port) ) exit( socket_strerror( socket_last_error() ) );
  else echo 'Socket_binded!'."\r\n";
   
  if( !socket_listen($socket, 10) ) exit( socket_strerror( socket_last_error() ) );
  else echo 'Socket_listen!'."\r\n";
   
  $connect = socket_accept($socket);
 /*  $secKey = $headers['Sec-WebSocket-Key'];
    $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
    $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
    "Upgrade: websocket\r\n" .
    "Connection: Upgrade\r\n" .
    "WebSocket-Origin: $host\r\n" .
    "WebSocket-Location: ws://localhost/taxi/taxichat/dispatcher/orders/Socket\r\n".
    "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
     socket_write($connect,$upgrade,strlen($upgrade)); */

  $result = socket_read($connect,1024);
   
  echo 'Common data: '.$result."\r\n";
   
  socket_write($connect,'You sending me: '.$result."\r\n");


  
  ?>
   