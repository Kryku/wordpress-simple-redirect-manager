# Simple Redirect Manager
## Description.
A simple plugin for Wordpress that allows you to quickly add redirects to website pages. This is just a minimal basic tool with the possibility of refinement, I use it in my practice, so it's enough. Why your own and not ready-made solution? The ability to edit and not load the site.
![Quick Buy Button](https://raw.githubusercontent.com/Kryku/wordpress-simple-redirect-manager/refs/heads/main/screenshots/srm.png)
## Features.
* Quickly create aliases for links.
* Ability to choose a redirect code.
* Quite low load and weight of the plugin.

## Installation.
1. Download the plugin.
2. Go to **Plugins > Add New** in the WordPress admin.
3. Upload the plugin file and activate it.

## Settings.
Go to Settings > Simple Redirect Manager in the WordPress admin.

## Here you can configure:
* Source URL - new link
* Target URL - the page to which you want to redirect
* Redirect Type - redirect code (you can customize and add your own)

### Redirections
##### 301 Redirection
A **301** redirect is a permanent redirect that tells clients that a requested resource has permanently moved to a new location. This is a common type of redirect that we can use after permanently shifting the resource.
##### 302 Redirection
A **302** redirect is a temporary redirect. This tells clients that a requested resource has been temporarily moved to a new location. This type of redirect is typically used when a resource is temporarily unavailable or has been temporarily moved for maintenance or other reasons.
##### **307** Redirection
A 307 redirect is another type of temporary redirect that we can use when the requested resource has been temporarily moved to a new location.
##### **308** Redirection
A 308 redirect is useful when we move the resource permanently to a new location. It’s similar to a 301 redirect, but it specifies we cannot change a POST request to a GET request in this type of redirection.
