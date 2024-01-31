<?php

namespace PlusItde\SoTypo3\Keywords;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class SeminerListKeyword extends AbstractKeyword
{
    protected $attributes = [
        "id",
        "Name",
        "content",
        "picture",
        "category",
        "eventdays",
        "location",
        "rate",
        "planned_events",
        "event_price",
        "event_type",
        "url",
        "data",
        "fix",
    ];

    public function processShortcode(
        string $keyword,
        array $attributes,
        string $match,
               $request = []
    ) {
        if (!empty($request["tx_short_code"]["id"])) {
            $html = $this->seminerDetails(
                $keyword,
                $attributes,
                $match,
                $request
            );
        } else {
            $html = $this->seminerListing(
                $keyword,
                $attributes,
                $match,
                $request
            );
        }

        return sprintf($html);
    }

    public function seminerDetails(
        string $keyword,
        array $attributes,
        string $match,
               $request = []
    ) {
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
        /*------------------ Get base url ---------------------*/
        $baseUrl =
            !empty($request) &&
            !empty($request["tx_short_code"]) &&
            !empty($request["tx_short_code"]["lang"])
                ? $this->getBaseUrl($request["tx_short_code"]["lang"])
                : $this->getBaseUrl();

        /*------------------ Api calling  ---------------------*/
        $url =
            $baseUrl .
            "/CMSSeminarList/" .
            $request["tx_short_code"]["id"] .
            "?limited_information=true&Type=Seminar";

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

        /*------------------ Api data  ---------------------*/

        $seminerDetail = $data["data"];
        $image = file_get_contents($seminerDetail["Image"]);
        $html =
            '<div class="basics-courses">
                <select name="language" id="language" class="language-dropdwon" onchange="languageChange(this.value, ' .
            htmlspecialchars(json_encode($request), ENT_QUOTES, "UTF-8") .
            ')"><i class="fas fa-globe-asia"></i>
                    <option value="en" ' .
            ($request["tx_short_code"]["lang"] == "en" ? "selected" : "") .
            '>English</option>
                    <option value="de" ' .
            ($request["tx_short_code"]["lang"] == "de" ? "selected" : "") .
            '>Germany</option>
                </select>
            <div class="breadcrumb-basics">
                <ul class="breadcrumb">
                    <li><a href="#">Seminer List</a></li>
                    <li>' .
            $seminerDetail["Name"] .
            '</li>
                </ul>
            </div>
            <div>
                <h2>' .
            $seminerDetail["Name"] .
            '</h2>
                <div class="section-row">
                    <div class="left-section">
                        <div class="navlinks">';

                    if(!empty($seminerDetail['ID']))
                    {
                        $widget_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                        $book_url = $this->getFrontendUrl().'/event-details?event_id=' . $seminerDetail["ID"] . '&add_to_cart=1&widget_url='.$widget_url.'/';

                        $html .= '<a href="'.$book_url.'" title="Book now" target="_blank" class="btn btn-black mr-two" rel="noreferrer">Book now</a>';
                    }

                            if (!empty($seminerDetail["Trainers"])){
                                $html .= '<a href="#tainer" class="link-underlined dial-darkest mr-two">Trainer</a>';
                            }
                            if (!empty($seminerDetail["Contact"])) {

                                $html .= '<a href = "#contact-us" class="dial-link-darkest mr-two" > Contact</a >';
                                }
        $html .= '</div>
                        <div>
                           <img src="data:image;base64,' .
            base64_encode($image) .
            '" alt="Seminar Module Image" class="img">
                        </div>';
        if (!empty($seminerDetail["Description"])) {
            $html .= '<p class="paragraph"> ' .$seminerDetail["Description"] ."</p>";
        } if (!empty($seminerDetail["Overview"])) {
            $html .= '<p class="paragraph"> <b> Overview </b> </p>';
            $html .= '<p class="paragraph"> ' .$seminerDetail["Overview"] ."</p>";
        }

        $html .= '</div><div class="right-section"><div class="rightsideinner-content"><div>';
        if (!empty($seminerDetail["Type"])) {
            $html .=' <p class="small-title">Type</p><p class="big-title">' .$seminerDetail["Type"] .'</p>';
        }
        if (!empty($seminerDetail["Price"])) {
            $html .='<p class="small-title">Price</p>
                     <p class="big-title">' .$seminerDetail["Price"] .'</p>';
        }
        if (!empty($seminerDetail["TargetGroup"])) {
            $html .= '<p class="small-title">Target Group</p>
                <p class="big-title">' .$seminerDetail["TargetGroup"] .'</p>';
        }
        if (!empty($seminerDetail["Startdate"])) {
            $html .= ' <p class="small-title" > Seminar Start Date </p >
                       <p class="big-title" >' .$seminerDetail["Startdate"] .'</p >';
        }
        if (!empty($seminerDetail["Enddate"])) {
            $html .=
                ' <p class="small-title" >Seminar End Date</p >
                  <p class="big-title" >' .$seminerDetail["Enddate"] .'</p >';
        }
        if (!empty($seminerDetail["Locations"])) {
            $html .=
                ' <p class="small-title" >Location</p >
                  <p class="big-title" >' .$seminerDetail["Locations"] .'</p >';
        }
        if (!empty($seminerDetail["Requirements"])) {
            $html .=
                ' <p class="small-title" >Requirements</p >
                  <p class="big-title" >' .$seminerDetail["Requirements"] .'</p >';
        }
        if (!empty($seminerDetail["Schedules"])) {
            $html .=
                ' <p class="small-title" >  Seminar Schedule</p >
                  <p class="big-title" >' .$seminerDetail["Schedules"]['start_time'] .' - '.$seminerDetail["Schedules"]['end_time'].'</p >';
        }
        $html .= '</div></div></div></div>';

        $html .= ' <div class="tainer-section" id="tainer">';
        if (!empty($seminerDetail["Trainers"])) {
            $html .= '<h2>Trainer</h2>';
        }

        $html .= '<div class="main-wraper">';
                      if (!empty($seminerDetail["Trainers"]))
                      {
                          $html .= ' <div class="left-section section-row">';
                          foreach ($seminerDetail["Trainers"] as $trainer)
                          {
                              $html .= '<div class="cards">
                                    <img src="https://www.dial.de/fileadmin/03_Academy/02_Trainer/DIAL_Academy_Seminar_Trainer_Bieckmann-Klaus.jpg" alt="" class="img">
                                    <div class="cards-body">
                                        <p><strong> '.$trainer["Fullname"].'</strong></p>
                                        <p> '.$trainer["Department"].'</p>
                                        <p class="cardsmall-title">'.$trainer["Profession"].'</p>
                                     
                                    </div>
                                    </div>';
                        
                          }
                          $html .= '</div>';
                      }
                      $html .= '</div></div></div></div>';
        return $html;
    }

    public function seminerListing(
        string $keyword,
        array $attributes,
        string $match,
               $request = []
    ) {
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
        /*------------------ Get base url ---------------------*/
        $baseUrl =
            !empty($request) &&
            !empty($request["tx_short_code"]) &&
            !empty($request["tx_short_code"]["lang"])
                ? $this->getBaseUrl($request["tx_short_code"]["lang"])
                : $this->getBaseUrl();

        /*------------------ set pagination  ---------------------*/

        $page =
            !empty($request) &&
            !empty($request["tx_short_code"]) &&
            !empty($request["tx_short_code"]["page"])
                ? $request["tx_short_code"]["page"]
                : 1;

        /*------------------Search  ---------------------*/
        $search =
            !empty($request) &&
            !empty($request["tx_short_code"]) &&
            !empty($request["tx_short_code"]["search"])
                ? $request["tx_short_code"]["search"]
                : "";

        /*------------------ Api calling  ---------------------*/
        if (!empty($search)) {
            $url = $baseUrl . "/CMSSeminarList?page=$page&Search=" . $search;
        } else {
            $url = $baseUrl ."/CMSSeminarList?page=" .$page;
        }

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
                $pagination = [];
                $content = $response->getBody()->getContents();
                $data = json_decode($response->getBody(), true);
            }
        }
        /*------------------ Api data  ---------------------*/


        $html =
            '<div class="container-table">
                <select name="language" id="language" class="language-dropdwon" onchange="languageChange(this.value, ' .
            htmlspecialchars(json_encode($request), ENT_QUOTES, "UTF-8") .
            ')"><i class="fas fa-globe-asia"></i>
                    <option value="en" ' .
            ($request["tx_short_code"]["lang"] == "en" ? "selected" : "") .
            '>English</option>
                    <option value="de" ' .
            ($request["tx_short_code"]["lang"] == "de" ? "selected" : "") .
            '>Germany</option>
                </select>
                <div class="title-withsearch">
                    <h2>Seminar Design and Technology</h2>
                    <div class="title-withbutton">
                        <input type="input" id="gsearch" name="gsearch" placeholder="Search..." class="search-input" VALUE="'.$search.'"><button type="button" class="btn-book1" onclick="searchFilter( ' .
            htmlspecialchars(json_encode($request), ENT_QUOTES, "UTF-8") .
            ')" value="' .
            $search .
            '"">Search</button>
                        </div> 
                </div>
                <div class="table-responsive">
                <table>
                <thead>
                <tr>
                <th>Title</th>
                <th>Date</th>
                <th>Category</th>
                <th>Language</th>
                <th>Type</th>
                <th>View</th>
                </tr>
                </thead>   
                <tbody>';
        if (isset($attributes["fix"]) && $attributes["fix"] == 1) {
            echo "<pre>";
            print_r($data);
        }
        if (!empty($data)) {
            $paginationHtml = "";
            $content_arr = json_decode($content);
            /*------------ pagination -------------*/
            if (!empty($content_arr) && count($data["data"]) > 0) {
                $pagination["current_page"] = $content_arr->current_page;
                $pagination["per_page"] = $content_arr->per_page;
                $pagination["first_page_url"] = $content_arr->first_page_url;
                $pagination["from"] = $content_arr->from;
                $pagination["to"] = $content_arr->to;
                $pagination["total"] = $content_arr->total;
                $pagination["last_page"] = $content_arr->last_page;
                $pagination["last_page_url"] = $content_arr->last_page_url;
                $pagination["next_page_url"] = $content_arr->next_page_url;
                $pagination["path"] = $content_arr->path;
                $pagination["prev_page_url"] = $content_arr->prev_page_url;
                $paginationHtml = $this->setPagination($pagination, $request);
            }
            if (!empty($data["data"])) {
                foreach ($data["data"] as $team) {
                    $book_url = "#";
                    $widget_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                    if(!empty($team['id']))
                    {
                        $book_url = $this->getFrontendUrl().'/event-details?event_id=' . $team["id"] . '&add_to_cart=1&widget_url='.$widget_url.'/';
                    }
                    $html .= "<tr>";
                    if (!empty($team["Name"])) {

                        $quer_id = '';

                        if (!empty($team["id"])) {
                            $request['tx_short_code']['id'] = $team['id'];
                            $quer_id = urldecode(http_build_query($request));
                        }

                        $name =
                            strlen($team["Name"]) > 20
                                ? substr($team["Name"], 0, 20) . "..."
                                : $team["Name"];
                        $html .=
                            '<td><strong><a href="?'.$quer_id.'" target="_blank">' .
                            $name .
                            "</strong></a></td>";
                    }

                    $date = !empty($team["StartDate"])
                        ? $team["StartDate"]
                        : "";
                    $html .= "<td>" . $date . "</td>";

                    $category = !empty($team["event_category_name"])
                        ? $team["event_category_name"]
                        : "";
                    $html .= "<td>" . $category . "</td>";

                    $lang =
                        !empty($request["tx_short_code"]["lang"]) &&
                        $request["tx_short_code"]["lang"] == "de"
                            ? "DE"
                            : "EN";
                    $html .= "<td>" . $lang . "</td>";

                    if (isset($team["Type"]) && $team["Type"]) {
                        $html .= "<td>" . $team["Type"] . "</td>";
                    }

                    $html .=
                        '<td><button type="button"  onclick="window.location.href = \'' . $book_url . '\'"  class="btn-book">Book Now</button></td>';
                    $html .= "</tr>";
                }
            } else {
                $html .=
                    '<tr><td align="center" colspan="6">No records found</td></tr>';
            }
            $html .= " </tbody> </table></div>";
            $html .= $paginationHtml;
        }

        $html .= "</div>";
        return $html;
    }
}
