<?php

namespace PlusItde\SoTypo3\Keywords;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class NextSeminarModuleKeyword extends AbstractKeyword
{
    protected $attributes = [
        "id",
        "fix",
        "title",
        "description",
        "image",
        "category",
        "tags",
        "price",
        "schedule",
        "url",
        "date",
        "type",
    ];

    public function processShortcode(
        string $keyword,
        array $attributes,
        string $match,
        $request = array()
    )
    {
        $token = $this->genarateToken();
        $requestFactory = GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Http\RequestFactory::class
        );
        $additionalOptions = [
            "headers" => [
                "Cache-Control" => "no-cache",
                "Authorization" => "Bearer " . $token,
            ],
            "allow_redirects" => false,
        ];
        $baseUrl = $this->getBaseUrl();
        $url =
            $baseUrl .
            "/CMSSeminarList/" .
            $attributes["id"] .
            "?api=true&Type=" .
            $attributes["type"];

        // Return a PSR-7 compliant response object
        $response = $requestFactory->request($url, "GET", $additionalOptions);

        // Get the content as a string on a successful request
        if ($response->getStatusCode() === 200) {
            if (
                strpos(
                    $response->getHeaderLine("Content-Type"),
                    "application/json"
                ) === 0
            ) {
                $content = $response->getBody()->getContents();
                $data = json_decode($response->getBody(), true);
            }
        }

        $html = '<div class="team-section">';
        if (isset($attributes["fix"]) && $attributes["fix"] == 1) {
            echo "<pre>";
            print_r($data);
        }

        if (!empty($data)) {

                $seminar = $data["data"];
                $id = $attributes["id"];

            if (!empty($seminar["ID"]) )
             {
                $html .= '<div class="team-member">';
                if (!empty($attributes["image"])) {
                    $image = file_get_contents($seminar["Image"]);
                    $html .= '<div class="module-img">';
                    if (!empty($seminar["Image"])) {
                        $html .= '<img src="data:image;base64,' . base64_encode($image) . '" alt="Seminar Module Image">';
                    } else {
                        $html .= '<img src="https://cutt.ly/Hwn6Y0NL" alt="Seminar Module Image">';
                    }

                    if (isset($attributes["date"]) && $attributes["date"]) {
                        $html .=
                            "<h4>Nächster Termin: " .
                            $seminar["date"] .
                            "</h4>";
                    }
                    $html .= "</div>";
                }

                $html .= '<div class="module-content">';
                if (isset($attributes["category"]) && $attributes["category"]) {
                    $html .= "<h4>" . $attributes["category"] . "</h4>";
                }
                if (
                    isset($attributes["event_name"]) &&
                    $attributes["event_name"]
                ) {
                    $html .=
                        ' <h2><a href="" style="cursor: auto; pointer-events: none;">' .
                        $seminar["event_name"] .
                        "</a></h2>";
                }

                if (isset($attributes["content"]) && $attributes["content"]) {
                    $content = $seminar["content"];
                    $desc = substr($content, 0, 220);
                    $html .= $desc;
                }
                $html .= "</div>";

                $html .= '<div class="module-tags">';
                $html .= "<ul>";
                if (
                    isset($attributes["eventdays"]) &&
                    $attributes["eventdays"]
                ) {
                    $html .=
                        '<li><img src="EXT:so_typo3/Resources/Public/clock-bl-icon.svg" alt="Tags Icon">' .
                        $seminar["eventDays"] .
                        "Tage</li>";
                }
                if (
                    isset($attributes["location"]) &&
                    $attributes["location"] == "Online"
                ) {
                    $html .=
                        '<li><img src="EXT:so_typo3/Resources/Public/display-bl-icon.svg" alt="Tags Icon">.' .
                        $attributes["location"] .
                        "</li>";
                } elseif (
                    isset($attributes["location"]) &&
                    $attributes["location"] == "Präsenztraining"
                ) {
                    $html .=
                        '<li><img src="EXT:so_typo3/Resources/Public/location-bl-icon.svg" alt="Tags Icon">.' .
                        $attributes["location"] .
                        "</li>";
                }
                if (isset($attributes["rate"])) {
                    $html .=
                        '<li><img src="EXT:so_typo3/Resources/Public/Star.svg" alt="Tags Icon">' .
                        $attributes["rate"] .
                        "</li>";
                }
                $html .= "</ul>";
                $html .= "</div>";
                if (
                    isset($attributes["event_price"]) &&
                    $attributes["event_price"]
                ) {
                    $html .= '<div class="module-price">';
                    $html .= "<h2>" . $seminar["event_price"] . "€</h2>";
                    $html .= "</div>";
                }
                if (
                    isset($attributes["planned_events"]) &&
                    $attributes["planned_events"]
                ) {
                    $html .= '<div class="module-option">';
                    $html .=
                        '<select name="module-option" id="module-option" class="' .
                        $id .
                        '_planned_seminar">';
                    foreach ($seminar["planned_events"] as $planned_events) {
                        $start_date = date_create(
                            $planned_events["event_startdate"]
                        );
                        $end_date = date_create(
                            $planned_events["event_enddate"]
                        );
                        $html .=
                            '<option data-planned-event-type="' .
                            $seminar["event_type"] .
                            '" data-planned-event-id="' .
                            $planned_events["id"] .
                            '" data-planned-event-name="' .
                            $planned_events["event_name"] .
                            '" data-planned-event-price="' .
                            $planned_events["event_price"] .
                            '" data-planned-event-start="' .
                            $planned_events["event_startdate"] .
                            '" data-planned-event-end="' .
                            $planned_events["event_enddate"] .
                            '" data-planned-event-location="' .
                            $planned_events["location"] .
                            '" value="' .
                            $planned_events["id"] .
                            '">' .
                            date_format($start_date, "d.m.Y") .
                            " - " .
                            date_format($end_date, "d.m.Y") .
                            " / Standort :" .
                            $planned_events["location"] .
                            "</option>";
                    }
                    $html .= "</select>";
                    $html .= "</div>";
                }
                $html .= '<div class="module-add-btn" id="so_module_btn">';
                $html .=
                    '<button id="' .
                    $id .
                    '_seminar" data-seminar-id="' .
                    $id .
                    '" class="btn-add"><img src="EXT:so_typo3/Resources/Public/bag-wt-icon.svg" alt="Add Button Icon"  onClick="addToCart()">Hinzufügen</button>';
                $html .= "</div>";
                $html .= "</div>";
            }

    }
        $html .= "</div>";
        return sprintf($html);
    }
}

