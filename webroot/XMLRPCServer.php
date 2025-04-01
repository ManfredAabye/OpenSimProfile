<?php
class XMLRPCServer
{
    private array $methods = [];

    // Methode registrieren
    public function registerMethod(string $methodName, callable $callback)
    {
        $this->methods[$methodName] = $callback;
    }

    // 🛠 Anfrage manuell parsen
    public function parseRequest(string $xml): array
    {
        preg_match('/<methodName>(.*?)<\/methodName>/', $xml, $methodMatches);
        preg_match_all('/<value>(.*?)<\/value>/', $xml, $paramMatches);

        $methodName = $methodMatches[1] ?? null;
        $params = $paramMatches[1] ?? [];

        return [$methodName, $params];
    }

    // 📡 Anfrage verarbeiten
    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("Nur POST-Anfragen erlaubt.");
        }

        $xml = file_get_contents("php://input");
        list($methodName, $params) = $this->parseRequest($xml);

        if (isset($this->methods[$methodName])) {
            $responseData = call_user_func($this->methods[$methodName], $params);
        } else {
            $responseData = ["error" => "Unbekannte Methode"];
        }

        header("Content-Type: text/xml");
        echo $this->generateResponse($responseData);
    }

    // 🔎 XML-RPC Antwort generieren
    private function generateResponse(array $response): string
    {
        $xml = "<methodResponse><params><param><value>" . htmlspecialchars(json_encode($response)) . "</value></param></params></methodResponse>";
        return $xml;
    }
}
?>
