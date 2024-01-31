<?php

namespace PlusItde\SoTypo3\Keywords;

use TYPO3\CMS\Core\Utility\GeneralUtility;


class TrainerListKeyword extends AbstractKeyword
{
    protected $attributes = [
        'theme',
        'widget_type',
        'limit',
        'maxwidth',
        'maxheight',
        'omit_script',
        'lang',
        'related',
        'border_color',
        'chrome',
        'aria_polite',
        'dnt',
    ];

    public function processShortcode(
        string $keyword,
        array  $attributes,
        string $match,
        $request = ''
    )
    {
        $token = $this->genarateToken();
        $requestFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Http\RequestFactory::class);
        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache', 'Authorization' => 'Bearer ' . $token],
            'allow_redirects' => false,

        ];
        $baseUrl = $this->getBaseUrl();
        $url = $baseUrl. "/trainer";

        // Return a PSR-7 compliant response object
        $response = $requestFactory->request($url, 'GET', $additionalOptions);


        // Get the content as a string on a successful request
        if ($response->getStatusCode() === 200) {
            if (strpos($response->getHeaderLine('Content-Type'), 'application/json') === 0) {
                $content = $response->getBody()->getContents();
                $data = json_decode($response->getBody(), true);
            }
        }
        $html = '<div class="team-section">';

        if(!empty($data))
        {

            foreach ($data['data'] as $team)
            {
                $html .= ' <div class="team-member"> ';
                $html .= '<img src="https://cutt.ly/Hwn6Y0NL" alt="Team Member 1">';
                $html .= '<h3>'.$team['Fullname'].'</h3>';
                $html .= '<p>'.$team['Profession'].'<p>';
                $html .= '<p>'.$team['Department'].'</p>';
                $html .= '<p>'.$team['Sector'].'</p>';
                $html .=  '</div>';
            }
        }
        $html .=  '</div>';


        return sprintf( $html);
    }
}
