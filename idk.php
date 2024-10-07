<?php

function searchXmlByCode($url, $searchCode) {
    // Načtení XML souboru pomocí CORS proxy
    $proxyUrl = 'https://corsproxy.io/?' . urlencode($url);
    
    // Načtení dat z proxy
    $xmlContent = file_get_contents($proxyUrl);
    
    if ($xmlContent === false) {
        die("Chyba při načítání XML dat z '$url'.");
    }

    $reader = new XMLReader();
    $reader->xml($xmlContent);

    $totalQuantity = 0;

    // Iterace přes XML elementy
    while ($reader->read()) {
        // Kontrola, zda je aktuální element <ITEM>
        if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'ITEM') {
            // Načtení aktuálního <ITEM>
            $itemNode = new SimpleXMLElement($reader->readOuterXML());

            // Získání hodnot <CODE> a <QUANTITY>
            $code = (string)$itemNode->CODE;
            $quantity = (int)$itemNode->QUANTITY;

            // Kontrola shody kódu
            if ($code === $searchCode) {
                $totalQuantity += $quantity; // Přičtení množství
            }
        }
    }

    // Uzavření XMLReader
    $reader->close();

    return $totalQuantity;
}

// URL XML souboru
$xmlUrl = 'https://www.henrymorgan.cz/export-orders-0sMgS8zSX8.xml'; // URL XML souboru
$searchCode = 'JD-AMN.20%'; // Hledaný kód

$totalQuantity = searchXmlByCode($xmlUrl, $searchCode);
echo "Celkový počet pro kód '$searchCode': $totalQuantity\n";
?>
