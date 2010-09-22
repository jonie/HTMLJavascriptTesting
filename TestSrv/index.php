<?
require_once('./lib/nusoap.php');

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Access-Control-Allow-Origin: *");

//mega method
function getdata($name) {
		$dbh = new PDO("sqlite:data.sdb");
		$tabCreateIfNoExists = 'CREATE TABLE IF NOT EXISTS [data](name TEXT, data TEXT)';
		$dbh->exec($tabCreateIfNoExists);
		
		$sql = 'SELECT * FROM [data] WHERE name=?';
		$selectSth 	= $dbh->prepare($sql);
		$selectSth->execute(array($name));
		$rv = $selectSth->fetchAll(PDO::FETCH_ASSOC);
		
		return json_encode($rv);
}

if(isset($_GET['soap']) && $_GET['soap']=='true') {
	//header("Content-type: application/soap+xml; charset=utf-8");

	$server = new soap_server();
	$server->configureWSDL('testapp', 'urn:testapp');

	$server->register('getdata',              // method name
		array('name' => 'xsd:string'),        // input parameters
		array('return' => 'xsd:string'),      // output parameters
		'urn:testapp',                        // namespace
		'urn:testapp#getdata',                // soapaction
		'rpc',                                // style
		'encoded',                            // use
		'Get data by name'            			// documentation
	);

	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$server->service(utf8_encode($HTTP_RAW_POST_DATA));
}
else {	//simple method
	echo getdata($_GET['name']);
}

