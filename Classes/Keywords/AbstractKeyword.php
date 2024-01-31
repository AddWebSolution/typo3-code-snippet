<?php

namespace PlusItde\SoTypo3\Keywords;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractKeyword
{
    /**
     * response
     *
     * The middleware reposnse object
     */
    protected $response;


    /**
     * body
     *
     * The page response as a string
     *
     * @var string
     */
    protected $body;

    /**
     * attributes
     *
     * A list of allowed attributes - everything else gets removed
     *
     * @var array
     */
    protected $attributes = [];

    private readonly ExtensionConfiguration $extensionConfiguration;
    private AssetCollector $assetCollector;

    public function __construct(Response $response, string $body)
    {
        $this->response = $response;
        $this->body = $body;
        $this->extensionConfiguration = new ExtensionConfiguration();
        $this->assetCollector = new AssetCollector();

    }

    abstract public function processShortcode(
        string $keyword,
        array  $attributes,
        string $match,
               $request = ''
    );
    public function removeAlienAttributes(&$attributes): void
    {
        foreach ($attributes as $key => $value) {
            if (!in_array($key, $this->attributes) && $key !== 'value') {
                unset($attributes[$key]);
            }
        }
    }

    public function genarateToken()
    {
        $this->assetCollector->addStyleSheet(
            'so_typo3',
            'EXT:simplyorg_ext/Resources/Public/Css/layout.min.css',
            ['data-foo' => 'bar'],
            ['priority' => true]
        );

        $token = '';
        // Fetch necessary configuration values
        $baseUrl = $this->getBaseUrl();
        $username = $this->extensionConfiguration->get('so_typo3', 'userName');
        $password = $this->extensionConfiguration->get('so_typo3', 'password');

        // Use the values to generate the token which will be expired after 45 min

        // Initiate the Request Factory, which allows to run multiple requests
        /** @var \TYPO3\CMS\Core\Http\RequestFactory $requestFactory */

        $url = $baseUrl . "/authenticate";
        $requestFactory = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Http\RequestFactory::class);
        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
            'allow_redirects' => false,
            'form_params' => [
                'email' => $username,
                'password' => $password,
            ]
        ];
        // Return a PSR-7 compliant response object
        $response = $requestFactory->request($url, 'POST', $additionalOptions);
        // Get the content as a string on a successful request
        if ($response->getStatusCode() === 200) {
            if (strpos($response->getHeaderLine('Content-Type'), 'application/json') === 0) {
                $content = $response->getBody()->getContents();
                $data = json_decode($response->getBody(), true);
                $token = $data['token'];
            }
        }
        return $token;
    }

// get base url From typo3 backend extension setting page and setting the API url.
    public function getBaseUrl($lang = 'en')
    {
        $getUrl = $this->extensionConfiguration->get('so_typo3', 'apiUrl');
        $baseUrl = $getUrl . "/" . $lang . "/api/v3";
        return $baseUrl;
    }

    public function getFrontendUrl()
    {
        $getUrl = $this->extensionConfiguration->get('so_typo3', 'frontendUrl');
        return $getUrl;
    }

    public function setPagination($data,$request=[])
    {
        $html = '<div class="pagination">';
        if( $data['current_page'] == 1)
        {
            $html .= '<a href="#"  class="page-link" disabled="disabled">&laquo;</a>';
        }
        else{
            $prev = $data['current_page']-1;
            $request['tx_short_code']['page'] = $prev;
            $query =  urldecode(http_build_query($request));
            $html .= '<a href="?' . $query . '"  class="page-link">&laquo;</a>';
        }
        $totalPage = ceil($data['total'] / $data['per_page']);
        for ($i = 1; $i <= $totalPage; $i++) {
            $html .= '<li class="page-item">';
            $request['tx_short_code']['page'] = $i;
            $query =  urldecode(http_build_query($request));
            $html .= '<a href="?'.$query.'" class="page-link';
            if ($data['current_page'] == $i) {
                $html .= ' active';
            }
            $html .= '">' . $i . '</a>';
            $html .= '</li>';
        }
        if( $data['current_page'] == $data['last_page'])
        {
            $html .= '<a href="#"  class="page-link" disabled="disabled">&raquo;</a>';
        }
        else{
            $next = $data['current_page']+1;
            $request['tx_short_code']['page'] = $next;
            $query =  urldecode(http_build_query($request));
            $html .= '<a href="?' . $query . '"  class="page-link">&raquo;</a>';
        }
        return $html;

    }

}

