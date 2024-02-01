# typo3-code-snippet
A TYPO3 extension for listing Seminer related details from simplyorg api.

First need to install this plugin with  composer by mentioned command below 

```
composer req plus-itde/so-typo3 :@dev
```

# Configure the extaintion with Api details  

configure the plugin by writting api related details such as Api (admin url), Frontend url,userid,password

# include plugin in typoscript template
Go to Typoscript menu and select Edit TypoScript record > Edit the whole TypoScript record > Advanced Options and include plugin 

# create page 
add short code 

# example of shortcode 

```
[seminarList :] 
```
# For Language specific we have attribute 
```
[seminarList lang='de'] 
```

# we are go to go ....






