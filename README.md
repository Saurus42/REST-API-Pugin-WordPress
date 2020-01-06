# REST-API-MENU

Menu operations using the REST API
Description

This is a WordPress extension that uses the REST API principles for client <-> server communication.The extension allows you to download and modify not only posts but also the main menu and media.
Routes

## All URL menus

GET    /api/v1/menus - Gives a list of menu items
POST   /api/v1/menus - Creates a new menu item
PUT    /api/v1/menus/:id - Modifies the selected menu item
DELETE /api/v1/menus/:id - Deletes the selected menu item

## All URL sub-menu

GET    /api/v1/sub-menus - Gives a list of sub menu items
POST   /api/v1/sub-menus - Creates a new sub menu item
PUT    /api/v1/sub-menus/:id - Modifies the selected sub menu item
DELETE /api/v1/sub-menus/:id - Deletes the selected sub menu item

## All URL post

GET    /api/v1/posts - Gives a list of sub menu items
POST   /api/v1/posts - Creates a new sub menu item
PUT    /api/v1/posts/:id - Modifies the selected sub menu item
DELETE /api/v1/posts/:id - Deletes the selected sub menu item
