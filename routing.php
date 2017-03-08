<?php
// @codingStandardsIgnoreFile
/**
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

/* ---------------------------------------------------------------------------
 * html, basic routes
 * -------------------------------------------------------------------------*/

\App::$slim->get('/',
    '\BO\Zmsapi\Index')
    ->setName("index");


/* ---------------------------------------------------------------------------
 * json
 * -------------------------------------------------------------------------*/


/**
 *  @swagger
 *  "/availability/{id}/":
 *      get:
 *          summary: Get an availability by id
 *          tags:
 *              - availability
 *          x-since: 2.4.0
 *          parameters:
 *              -   name: id
 *                  description: availability number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/availability.json"
 *              404:
 *                  description: "availability id does not exists"
 */
\App::$slim->get('/availability/{id:\d{1,11}}/',
    '\BO\Zmsapi\AvailabilityGet')
    ->setName("AvailabilityGet");

/**
 *  @swagger
 *  "/availability/":
 *      post:
 *          summary: Create or update availabilities. If an entity has an id, an update is performed
 *          tags:
 *              - availability
 *          parameters:
 *              -   name: availability
 *                  description: availabilityList data to update
 *                  in: body
 *                  schema:
 *                      type: array
 *                      items:
 *                          $ref: "schema/availability.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/availability.json"
 *              404:
 *                  description: "availability id does not exists"
 */
\App::$slim->post('/availability/',
    '\BO\Zmsapi\AvailabilityAdd')
    ->setName("AvailabilityAdd");

/**
 *  @swagger
 *  "/availability/{id}/":
 *      post:
 *          summary: Update an availability
 *          tags:
 *              - availability
 *          parameters:
 *              -   name: id
 *                  description: availability number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: availability
 *                  description: availability data to update
 *                  in: body
 *                  schema:
 *                      $ref: "schema/availability.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/availability.json"
 *              404:
 *                  description: "availability id does not exists"
 */
\App::$slim->post('/availability/{id:\d{1,11}}/',
    '\BO\Zmsapi\AvailabilityUpdate')
    ->setName("AvailabilityUpdate");

/**
 *  @swagger
 *  "/availability/{id}/":
 *      delete:
 *          summary: Deletes an availability
 *          tags:
 *              - availability
 *          parameters:
 *              -   name: id
 *                  description: availability number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success, returns deleted object"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/availability.json"
 *              404:
 *                  description: "availability id does not exists"
 */
\App::$slim->delete('/availability/{id:\d{1,11}}/',
    '\BO\Zmsapi\AvailabilityDelete')
    ->setName("AvailabilityDelete");

/**
*  @swagger
*  "/calendar/":
*      post:
*          summary: Get a list of available days for appointments
*          tags:
*              - calendar
*          parameters:
*              -   name: calendar
*                  description: data for finding available days
*                  required: true
*                  in: body
*                  schema:
*                      $ref: "schema/calendar.json"
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
*          responses:
*              200:
*                  description: get an updated calendar objects with updated days list
*                  schema:
*                      type: object
*                      properties:
*                          meta:
*                              $ref: "schema/metaresult.json"
*                          data:
*                              $ref: "schema/calendar.json"
*              404:
*                  description: "Could not find any available days"
*                  schema:
*                      type: object
*                      properties:
*                          meta:
*                              $ref: "schema/metaresult.json"
*                          data:
*                              $ref: "schema/calendar.json"
*/
\App::$slim->post('/calendar/',
    '\BO\Zmsapi\CalendarGet')
    ->setName("CalendarGet");

/**
*  @swagger
*  "/calldisplay/":
*      post:
*          summary: Get preferences for a calldisplay
*          tags:
*              - calldisplay
*          parameters:
*              -   name: calldisplay
*                  description: data containing scopes and clusters
*                  required: true
*                  in: body
*                  schema:
*                      $ref: "schema/calldisplay.json"
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
*          responses:
*              200:
*                  description: get an updated calldislay object with updated scope and cluster list
*                  schema:
*                      type: object
*                      properties:
*                          meta:
*                              $ref: "schema/metaresult.json"
*                          data:
*                              $ref: "schema/calldisplay.json"
*              404:
*                  description: "Could not find a given cluster or scope, see metaresult"
*                  schema:
*                      type: object
*                      properties:
*                          meta:
*                              $ref: "schema/metaresult.json"
*/
\App::$slim->post('/calldisplay/',
    '\BO\Zmsapi\CalldisplayGet')
    ->setName("CalldisplayGet");

/**
*  @swagger
*  "/calldisplay/queue/":
*      post:
*          summary: Get queue for a calldisplay
*          tags:
*              - calldisplay
*              - queue
*          parameters:
*              -   name: calldisplay
*                  description: data containing scopes and clusters
*                  required: true
*                  in: body
*                  schema:
*                      $ref: "schema/calldisplay.json"
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
*          responses:
*              200:
*                  description: get a list of queue entries
*                  schema:
*                      type: object
*                      properties:
*                          meta:
*                              $ref: "schema/metaresult.json"
*                          data:
*                              type: array
*                              items:
*                                  $ref: "schema/queue.json"
*              404:
*                  description: "Could not find a given cluster or scope, see metaresult"
*                  schema:
*                      type: object
*                      properties:
*                          meta:
*                              $ref: "schema/metaresult.json"
*/
\App::$slim->post('/calldisplay/queue/',
    '\BO\Zmsapi\CalldisplayQueue')
    ->setName("CalldisplayQueue");

/**
 *  @swagger
 *  "/cluster/{id}/":
 *      get:
 *          summary: Get an cluster by id
 *          tags:
 *              - cluster
 *          parameters:
 *              -   name: id
 *                  description: cluster number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/cluster.json"
 *              404:
 *                  description: "cluster id does not exists"
 */
\App::$slim->get('/cluster/{id:\d{1,11}}/',
    '\BO\Zmsapi\ClusterGet')
    ->setName("ClusterGet");

/**
 *  @swagger
 *  "/cluster/{id}/":
 *      post:
 *          summary: Update an cluster
 *          tags:
 *              - cluster
 *          parameters:
 *              -   name: id
 *                  description: cluster number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: cluster
 *                  description: cluster data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/cluster.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/cluster.json"
 *              404:
 *                  description: "cluster id does not exists"
 */
\App::$slim->post('/cluster/{id:\d{1,11}}/',
    '\BO\Zmsapi\ClusterUpdate')
    ->setName("ClusterUpdate");

/**
 *  @swagger
 *  "/cluster/{id}/":
 *      delete:
 *          summary: Deletes an cluster
 *          tags:
 *              - cluster
 *          parameters:
 *              -   name: id
 *                  description: cluster number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *              404:
 *                  description: "cluster id does not exists"
 */
\App::$slim->delete('/cluster/{id:\d{1,11}}/',
    '\BO\Zmsapi\ClusterDelete')
    ->setName("ClusterDelete");

/**
 *  @swagger
 *  "/cluster/{id}/queue/":
 *      get:
 *          summary: Get a waiting queue for a cluster
 *          tags:
 *              - cluster
 *              - queue
 *          parameters:
 *              -   name: id
 *                  description: cluster number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/queue.json"
 *              404:
 *                  description: "cluster id does not exists"
 */
\App::$slim->get('/cluster/{id:\d{1,11}}/queue/',
    '\BO\Zmsapi\ClusterQueue')
    ->setName("ClusterQueue");

/**
 *  @swagger
 *  "/cluster/{id}/waitingnumber/{hash}/":
 *      get:
 *          summary: Get a waitingNumber according to scope preferences in cluster
 *          tags:
 *              - cluster
 *              - process
 *          parameters:
 *              -   name: id
 *                  description: cluster number
 *                  required: true
 *                  in: path
 *                  type: integer
 *              -   name: hash
 *                  description: valid ticketprinter hash
 *                  required: true
 *                  in: path
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/process.json"
 *              403:
 *                  description: "hash is not valid"
 *              404:
 *                  description: "cluster id does not exists"
 */
\App::$slim->get('/cluster/{id:\d{1,11}}/waitingnumber/{hash}/',
    '\BO\Zmsapi\TicketprinterWaitingnumberByCluster')
    ->setName("TicketprinterWaitingnumberByCluster:");

/**
 *  @swagger
 *  "/cluster/{id}/imagedata/calldisplay/":
 *      get:
 *          summary: get image data by cluster id for calldisplay image
 *          tags:
 *              - cluster
 *              - mimepart
 *          parameters:
 *              -   name: id
 *                  description: number of cluster
 *                  in: path
 *                  required: true
 *                  type: integer
 *          responses:
 *              200:
 *                  description: get existing imagedata by cluster id
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/mimepart.json"
 *              404:
 *                  description: "Could not find given cluster"
 */
\App::$slim->get('/cluster/{id:\d{1,4}}/imagedata/calldisplay/',
    '\BO\Zmsapi\ClusterCalldisplayImageDataGet')
    ->setName("ClusterCalldisplayImageDataGet");

/**
 *  @swagger
 *  "/cluster/{id}/imagedata/calldisplay/":
 *      post:
 *          summary: upload and get image data by cluster id for calldisplay image
 *          tags:
 *              - cluster
 *              - mimepart
 *          parameters:
 *              -   name: id
 *                  description: number of cluster
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: mimepart
 *                  description: mimepart image data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/mimepart.json"
 *          responses:
 *              200:
 *                  description: get an updated mimepart entity
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/mimepart.json"
 *              404:
 *                  description: "Could not find given cluster"
 */
\App::$slim->post('/cluster/{id:\d{1,4}}/imagedata/calldisplay/',
    '\BO\Zmsapi\ClusterCalldisplayImageDataUpdate')
    ->setName("ClusterCalldisplayImageDataUpdate");


/**
 *  @swagger
 *  "/cluster/{id}/organisation/":
 *      get:
 *          summary: Get an organisation by clusterId.
 *          tags:
 *              - cluster
 *              - organisation
 *          parameters:
 *              -   name: id
 *                  description: cluster number
 *                  in: path
 *                  required: true
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/organisation.json"
 *              404:
 *                  description: "organisation id does not exists"
 */
\App::$slim->get('/cluster/{id:\d{1,4}}/organisation/',
    '\BO\Zmsapi\OrganisationByCluster')
    ->setName("OrganisationByCluster");

/**
 *  @swagger
 *  "/config/":
 *      get:
 *          summary: Get config
 *          tags:
 *              - config
 *          parameters:
 *              -   name: X-Token
 *                  description: Secure Token
 *                  required: true
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/config.json"
 *              404:
 *                  description: "config not found"
 *              401:
 *                  description: "authentification failed"
 */
\App::$slim->get('/config/',
    '\BO\Zmsapi\ConfigGet')
    ->setName("ConfigGet");

/**
 *  @swagger
 *  "/dayoff/{year}/":
 *      get:
 *          summary: Get a list of common free days for a given year
 *          tags:
 *              - dayoff
 *          parameters:
 *              -   name: year
 *                  description: year for the free days
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/dayoff.json"
 *              404:
 *                  description: "year out of range"
 */
\App::$slim->get('/dayoff/{year:2\d{3,3}}/',
    '\BO\Zmsapi\DayoffList')
    ->setName("DayoffList");

/**
 *  @swagger
 *  "/dayoff/{year}/":
 *      post:
 *          summary: Update list of common free days
 *          tags:
 *              - dayoff
 *          parameters:
 *              -   name: year
 *                  description: year for the free days
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: dayoff
 *                  description: dayoff data to update
 *                  in: body
 *                  schema:
 *                      type: array
 *                      items:
 *                          $ref: "schema/dayoff.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/dayoff.json"
 *              404:
 *                  description: "year out of range"
 */
\App::$slim->post('/dayoff/{year:2\d{3,3}}/',
    '\BO\Zmsapi\DayoffUpdate')
    ->setName("DayoffUpdate");

/**
 *  @swagger
 *  "/department/":
 *      get:
 *          summary: Get a list of departments
 *          tags:
 *              - department
 *          parameters:
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/department.json"
 */
\App::$slim->get('/department/',
    '\BO\Zmsapi\DepartmentList')
    ->setName("DepartmentList");

/**
 *  @swagger
 *  "/department/{id}/":
 *      get:
 *          summary: Get an department by id
 *          tags:
 *              - department
 *          parameters:
 *              -   name: id
 *                  description: department number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/department.json"
 *              404:
 *                  description: "department id does not exists"
 */
\App::$slim->get('/department/{id:\d{1,11}}/',
    '\BO\Zmsapi\DepartmentGet')
    ->setName("DepartmentGet");

/**
 *  @swagger
 *  "/department/{id}/":
 *      post:
 *          summary: Update an department
 *          tags:
 *              - department
 *          parameters:
 *              -   name: id
 *                  description: department number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: department
 *                  description: department data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/department.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/department.json"
 *              404:
 *                  description: "department id does not exists"
 */
\App::$slim->post('/department/{id:\d{1,11}}/',
    '\BO\Zmsapi\DepartmentUpdate')
    ->setName("DepartmentUpdate");

/**
 *  @swagger
 *  "/department/{id}/":
 *      delete:
 *          summary: Deletes an department
 *          tags:
 *              - department
 *          parameters:
 *              -   name: id
 *                  description: department number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *              404:
 *                  description: "department id does not exists"
 */
\App::$slim->delete('/department/{id:\d{1,11}}/',
    '\BO\Zmsapi\DepartmentDelete')
    ->setName("DepartmentDelete");

/**
 *  @swagger
 *  "/department/{id}/scope/":
 *      post:
 *          summary: Add a new scope
 *          tags:
 *              - department
 *              - scope
 *          parameters:
 *              -   name: id
 *                  description: department number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: scope
 *                  description: scope data to add
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/scope.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/scope.json"
 *              404:
 *                  description: "Missing required properties in the scope"
 */
\App::$slim->post('/department/{id:\d{1,11}}/scope/',
    '\BO\Zmsapi\DepartmentAddScope')
    ->setName("DepartmentAddScope");

/**
 *  @swagger
 *  "/department/{id}/cluster/":
 *      post:
 *          summary: Add a new cluster
 *          tags:
 *              - department
 *              - cluster
 *          parameters:
 *              -   name: id
 *                  description: department number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: cluster
 *                  description: cluster data to add
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/cluster.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/cluster.json"
 *              404:
 *                  description: "Missing required properties in the cluster"
 */
\App::$slim->post('/department/{id:\d{1,11}}/cluster/',
                  '\BO\Zmsapi\DepartmentAddCluster')
    ->setName("DepartmentAddCluster");

/**
 *  @swagger
 *  "/department/{id}/organisation/":
 *      get:
 *          summary: Get the parent organisation for a department
 *          tags:
 *              - department
 *              - organisation
 *          parameters:
 *              -   name: id
 *                  description: department number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/organisation.json"
 */
\App::$slim->get('/department/{id:\d{1,11}}/organisation/',
    '\BO\Zmsapi\DepartmentOrganisation')
    ->setName("DepartmentOrganisation");

/**
 *  @swagger
 *  "/department/{id}/useraccount/":
 *      get:
 *          summary: Get a list of useraccounts for a department
 *          tags:
 *              - department
 *              - useraccount
 *          parameters:
 *              -   name: id
 *                  description: department number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/useraccount.json"
 */
\App::$slim->get('/department/{id:\d{1,11}}/useraccount/',
    '\BO\Zmsapi\DepartmentUseraccountList')
    ->setName("DepartmentUseraccountList");

/**
 *  @swagger
 *  "/mails/":
 *      get:
 *          summary: get a list of mails in the send queue
 *          tags:
 *              - mail
 *          parameters:
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: returns a list, might be empty
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/mail.json"
 */
\App::$slim->get('/mails/',
    '\BO\Zmsapi\MailList')
    ->setName("MailList");


/**
 *  @swagger
 *  "/mails/":
 *      post:
 *          summary: Add a mail to the send queue
 *          tags:
 *              - mail
 *          parameters:
 *              -   name: notification
 *                  description: mail data to send
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/mail.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: mail accepted
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/mail.json"
 *              400:
 *                  description: "Missing required properties in the notification"
 */
\App::$slim->post('/mails/',
    '\BO\Zmsapi\MailAdd')
    ->setName("MailAdd");

/**
 *  @swagger
 *  "/mails/{id}/":
 *      delete:
 *          summary: delete a mail in the send queue
 *          tags:
 *              - mail
 *          parameters:
 *              -   name: id
 *                  description: mail number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: succesfully deleted
 *              404:
 *                  description: "could not find mail or mail already sent"
 */
\App::$slim->delete('/mails/{id:\d{1,11}}/',
    '\BO\Zmsapi\MailDelete')
    ->setName("MailDelete");


/**
 *  @swagger
 *  "/notification/":
 *      get:
 *          summary: get a list of notifications in the send queue
 *          tags:
 *              - notification
 *          parameters:
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: returns a list, might be empty
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/notification.json"
 */
\App::$slim->get('/notification/',
    '\BO\Zmsapi\NotificationList')
    ->setName("NotificationList");



/**
 *  @swagger
 *  "/notification/":
 *      post:
 *          summary: Add a notification to the send queue
 *          tags:
 *              - notification
 *          parameters:
 *              -   name: notification
 *                  description: notification data to send
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/notification.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: notification accepted
 *              400:
 *                  description: "Missing required properties in the notification"
 */
\App::$slim->post('/notification/',
    '\BO\Zmsapi\NotificationAdd')
    ->setName("NotificationAdd");

/**
 *  @swagger
 *  "/notification/{id}/":
 *      delete:
 *          summary: delete a notification in the send queue
 *          tags:
 *              - notification
 *          parameters:
 *              -   name: id
 *                  description: notification number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: succesfully deleted
 *              404:
 *                  description: "could not find notification or notification already sent"
 */
\App::$slim->delete('/notification/{id:\d{1,11}}/',
    '\BO\Zmsapi\NotificationDelete')
    ->setName("NotificationDelete");


/**
 *  @swagger
 *  "/owner/":
 *      get:
 *          summary: Get a list of owners
 *          tags:
 *              - owner
 *          parameters:
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/owner.json"
 */
\App::$slim->get('/owner/',
    '\BO\Zmsapi\OwnerList')
    ->setName("OwnerList");

/**
 *  @swagger
 *  "/owner/{id}/":
 *      get:
 *          summary: Get an owner by id
 *          tags:
 *              - owner
 *          parameters:
 *              -   name: id
 *                  description: owner number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/owner.json"
 *              404:
 *                  description: "owner id does not exists"
 */
\App::$slim->get('/owner/{id:\d{1,11}}/',
    '\BO\Zmsapi\OwnerGet')
    ->setName("OwnerGet");

/**
 *  @swagger
 *  "/owner/{id}/":
 *      post:
 *          summary: Update an owner
 *          tags:
 *              - owner
 *          parameters:
 *              -   name: id
 *                  description: owner number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: owner
 *                  description: owner data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/owner.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/owner.json"
 *              404:
 *                  description: "owner id does not exists"
 */
\App::$slim->post('/owner/{id:\d{1,11}}/',
    '\BO\Zmsapi\OwnerUpdate')
    ->setName("OwnerUpdate");

/**
 *  @swagger
 *  "/owner/":
 *      post:
 *          summary: Add a new owner
 *          tags:
 *              - owner
 *          parameters:
 *              -   name: owner
 *                  description: owner data to add
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/owner.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/department.json"
 *              404:
 *                  description: "Missing required properties in the owner"
 */
\App::$slim->post('/owner/',
    '\BO\Zmsapi\OwnerAdd')
    ->setName("OwnerAdd");


/**
 *  @swagger
 *  "/owner/{id}/":
 *      delete:
 *          summary: Deletes an owner
 *          tags:
 *              - owner
 *          parameters:
 *              -   name: id
 *                  description: owner number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *              404:
 *                  description: "owner id does not exists"
 */
\App::$slim->delete('/owner/{id:\d{1,11}}/',
    '\BO\Zmsapi\OwnerDelete')
    ->setName("OwnerDelete");

/**
 *  @swagger
 *  "/owner/{id}/organisation/":
 *      post:
 *          summary: Add a new organisation
 *          tags:
 *              - owner
 *              - organisation
 *          parameters:
 *              -   name: id
 *                  description: owner number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: organisation
 *                  description: organisation data to add
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/organisation.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/organisation.json"
 *              404:
 *                  description: "Missing required properties in the organisation"
 */
\App::$slim->post('/owner/{id:\d{1,11}}/organisation/',
    '\BO\Zmsapi\OwnerAddOrganisation')
    ->setName("OwnerAddOrganisation");


/**
 *  @swagger
 *  "/organisation/":
 *      get:
 *          summary: Get a list of organisations
 *          tags:
 *              - organisation
 *          parameters:
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/organisation.json"
 */
\App::$slim->get('/organisation/',
    '\BO\Zmsapi\OrganisationList')
    ->setName("OrganisationList");

/**
 *  @swagger
 *  "/organisation/{id}/":
 *      get:
 *          summary: Get an organisation by id
 *          tags:
 *              - organisation
 *          parameters:
 *              -   name: id
 *                  description: organisation number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/organisation.json"
 *              404:
 *                  description: "organisation id does not exists"
 */
\App::$slim->get('/organisation/{id:\d{1,11}}/',
    '\BO\Zmsapi\OrganisationGet')
    ->setName("OrganisationGet");

/**
 *  @swagger
 *  "/organisation/{id}/":
 *      post:
 *          summary: Update an organisation
 *          tags:
 *              - organisation
 *          parameters:
 *              -   name: id
 *                  description: organisation number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: organisation
 *                  description: organisation data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/organisation.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/organisation.json"
 *              404:
 *                  description: "organisation id does not exists"
 */
\App::$slim->post('/organisation/{id:\d{1,11}}/',
    '\BO\Zmsapi\OrganisationUpdate')
    ->setName("OrganisationUpdate");

/**
 *  @swagger
 *  "/organisation/{id}/":
 *      delete:
 *          summary: Deletes an organisation
 *          tags:
 *              - organisation
 *          parameters:
 *              -   name: id
 *                  description: organisation number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *              404:
 *                  description: "organisation id does not exists"
 */
\App::$slim->delete('/organisation/{id:\d{1,11}}/',
    '\BO\Zmsapi\OrganisationDelete')
    ->setName("OrganisationDelete");

/**
 *  @swagger
 *  "/organisation/{id}/hash/":
 *      get:
 *          summary: Get a hash to identify a ticketprinter. Usually a browser requests a hash once and stores it in a cookie.
 *          tags:
 *              - organisation
 *              - ticketprinter
 *          parameters:
 *              -   name: id
 *                  description: organisation number
 *                  in: path
 *                  required: true
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/ticketprinter.json"
 *              404:
 *                  description: "organisation id does not exists"
 */
\App::$slim->get('/organisation/{id:\d{1,11}}/hash/',
    '\BO\Zmsapi\OrganisationHash')
    ->setName("OrganisationHash");

/**
 *  @swagger
 *  "/organisation/{id}/department/":
 *      post:
 *          summary: Add a new department
 *          tags:
 *              - organisation
 *              - department
 *          parameters:
 *              -   name: id
 *                  description: organisation number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: department
 *                  description: department data to add
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/department.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/department.json"
 *              404:
 *                  description: "Missing required properties in the department"
 */
\App::$slim->post('/organisation/{id:\d{1,11}}/department/',
    '\BO\Zmsapi\OrganisationAddDepartment')
    ->setName("OrganisationAddDepartment");

/**
 *  @swagger
 *  "/process/{id}/{authKey}/":
 *      get:
 *          summary: Get a process
 *          tags:
 *              - process
 *          parameters:
 *              -   name: id
 *                  description: process number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: authKey
 *                  description: authentication key or name
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/process.json"
 *              403:
 *                  description: "authkey does not match"
 *              404:
 *                  description: "process id does not exists"
 */
\App::$slim->get('/process/{id:\d{1,11}}/{authKey}/',
    '\BO\Zmsapi\ProcessGet')
    ->setName("ProcessGet");


/**
 *  @swagger
 *  "/process/{id}/{authKey}/ics/":
 *      get:
 *          summary: Get an ICS-File for a process
 *          tags:
 *              - process
 *          parameters:
 *              -   name: id
 *                  description: process number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: authKey
 *                  description: authentication key
 *                  in: path
 *                  required: true
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: object
 *                              properties:
 *                                  content:
 *                                      type: string
 *                                      description: "base64 encoded ICS file"
 *              403:
 *                  description: "authkey does not match"
 *              404:
 *                  description: "process id does not exists"
 */
\App::$slim->get('/process/{id:\d{1,11}}/{authKey}/ics/',
    '\BO\Zmsapi\ProcessIcs')
    ->setName("ProcessIcs");


/**
 *  @swagger
 *  "/process/{id}/{authKey}/":
 *      post:
 *          summary: Update a process but does not send any mails or notifications on status changes
 *          tags:
 *              - process
 *          parameters:
 *              -   name: id
 *                  description: process number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: authKey
 *                  description: authentication key
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: process
 *                  description: process data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/process.json"
 *          responses:
 *              200:
 *                  description: "success, there might be changes on the object or added information. Use the response for further action with the process"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/process.json"
 *              400:
 *                  description: "Invalid input"
 *              403:
 *                  description: "forbidden, authkey does not match or status changes, only data may be changed"
 *              404:
 *                  description: "process id does not exists"
 */
\App::$slim->post('/process/{id:\d{1,11}}/{authKey}/',
    '\BO\Zmsapi\ProcessUpdate')
    ->setName("ProcessUpdate");

/**
 *  @swagger
 *  "/process/{id}/{authKey}/confirmation/mail/":
 *      post:
 *          summary: send mail on confirmed process. Depending on config, if no mail is send, an empty mail is returned.
 *          tags:
 *              - process
 *              - mail
 *          parameters:
 *              -   name: id
 *                  description: process number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: authKey
 *                  description: authentication key
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: process
 *                  description: process data for building mail
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/process.json"
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/mail.json"
 *              400:
 *                  description: "Invalid input"
 *              403:
 *                  description: "forbidden, authkey does not match or status changes, only data may be changed"
 *              404:
 *                  description: "process id does not exists"
 */
\App::$slim->post('/process/{id:\d{1,11}}/{authKey}/confirmation/mail/',
    '\BO\Zmsapi\ProcessConfirmationMail')
    ->setName("ProcessConfirmationMail");

/**
 *  @swagger
 *  "/process/{id}/{authKey}/delete/mail/":
 *      post:
 *          summary: send mail on delete process. Depending on config, if no mail is send, an empty mail is returned.
 *          tags:
 *              - process
 *              - mail
 *          parameters:
 *              -   name: id
 *                  description: process number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: authKey
 *                  description: authentication key
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: process
 *                  description: process data for building mail
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/process.json"
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/mail.json"
 *              400:
 *                  description: "Invalid input"
 *              403:
 *                  description: "forbidden, authkey does not match or status changes, only data may be changed"
 *              404:
 *                  description: "process id does not exists"
 */
\App::$slim->post('/process/{id:\d{1,11}}/{authKey}/delete/mail/',
    '\BO\Zmsapi\ProcessDeleteMail')
    ->setName("ProcessDeleteMail");

/**
 *  @swagger
 *  "/process/{id}/{authKey}/confirmation/notification/":
 *      post:
 *          summary: send notification on confirmed process
 *          tags:
 *              - process
 *              - notification
 *          parameters:
 *              -   name: id
 *                  description: process number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: authKey
 *                  description: authentication key
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: process
 *                  description: process data for building notification
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/process.json"
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/process.json"
 *              400:
 *                  description: "Invalid input"
 *              403:
 *                  description: "forbidden, authkey does not match or status changes, only data may be changed"
 *              404:
 *                  description: "process id does not exists"
 */
\App::$slim->post('/process/{id:\d{1,11}}/{authKey}/confirmation/notification/',
    '\BO\Zmsapi\ProcessConfirmationNotification')
    ->setName("ProcessConfirmationNotification");

/**
 *  @swagger
 *  "/process/{id}/{authKey}/":
 *      delete:
 *          summary: Deletes a process but does not send any mails or notifications
 *          tags:
 *              - process
 *          parameters:
 *              -   name: id
 *                  description: process number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: authKey
 *                  description: authentication key
 *                  in: path
 *                  required: true
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success, there might be changes on the object or added information. Use the response for further action with the process"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/process.json"
 *              403:
 *                  description: "authkey does not match"
 *              404:
 *                  description: "process id does not exists"
 */
\App::$slim->delete('/process/{id:\d{1,11}}/{authKey}/',
    '\BO\Zmsapi\ProcessDelete')
    ->setName("ProcessDelete");

/**
 *  @swagger
 *  "/process/status/free/":
 *      post:
 *          summary: Get a list of free processes for a given day
 *          tags:
 *              - calendar
 *              - process
 *          parameters:
 *              -   name: calendar
 *                  description: data for finding available processes, try to restrict data to one day, if possible
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/calendar.json"
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: get a list of available processes
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/process.json"
 *              404:
 *                  description: "Could not find any available processes, returns empty list"
 *                  schema:
 *                      type: array
 *                      items:
 *                          $ref: "schema/process.json"
 */
\App::$slim->post('/process/status/free/',
    '\BO\Zmsapi\ProcessFree')
    ->setName("ProcessFree");

/**
 *  @swagger
 *  "/process/status/reserved/":
 *      get:
 *          summary: Get a list of reserved processes
 *          tags:
 *              - process
 *          parameters:
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: get a list of processes
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/process.json"
 *              404:
 *                  description: "Could not find any processes, returns empty list"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  type: object
 */
\App::$slim->get('/process/status/reserved/',
    '\BO\Zmsapi\ProcessReservedList')
    ->setName("ProcessReservedList");

/**
 *  @swagger
 *  "/process/status/reserved/":
 *      post:
 *          summary: Try to reserve the appointments in a process
 *          tags:
 *              - process
 *          parameters:
 *              -   name: process
 *                  description: process data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/process.json"
 *          responses:
 *              200:
 *                  description: get a list of processes
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/process.json"
 *              400:
 *                  description: "Invalid input"
 *              404:
 *                  description: "Failed to reserve a process"
 */
\App::$slim->post('/process/status/reserved/',
    '\BO\Zmsapi\ProcessReserve')
    ->setName("ProcessReserve");

/**
 *  @swagger
 *  "/process/status/confirmed/":
 *      post:
 *          summary: Try to confirm a process, changes status from reservered to confirmed
 *          tags:
 *              - process
 *          parameters:
 *              -   name: process
 *                  description: process data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/process.json"
 *          responses:
 *              200:
 *                  description: process is confirmed, notifications and mails sent according to preferences
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/process.json"
 *              302:
 *                  description: "Redirects to /processes/status/reserved/ since the given process does not exists in the list (any longer)"
 *              400:
 *                  description: "Invalid input"
 *              403:
 *                  description: "authkey does not match"
 */
\App::$slim->post('/process/status/confirmed/',
    '\BO\Zmsapi\ProcessConfirm')
    ->setName("ProcessConfirm");

/**
 *  @swagger
 *  "/provider/{source}/{id}/":
 *      get:
 *          summary: Get an provider by id
 *          tags:
 *              - provider
 *          parameters:
 *              -   name: source
 *                  description: provider source like 'dldb'
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: id
 *                  description: provider number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/provider.json"
 *              404:
 *                  description: "provider id for source does not exists"
 */
\App::$slim->get('/provider/{source}/{id:\d{1,11}}/',
    '\BO\Zmsapi\ProviderGet')
    ->setName("ProviderGet");

/**
 *  @swagger
 *  "/provider/{source}/{id}/scopes/":
 *      get:
 *          summary: Get a list of scope by provider ID
 *          tags:
 *              - provider
 *              - scope
 *          parameters:
 *              -   name: source
 *                  description: provider source like 'dldb'
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: id
 *                  description: provider number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/scope.json"
 *              404:
 *                  description: "provider id does not exists"
 */
\App::$slim->get('/provider/{source}/{id:\d{1,11}}/scopes/',
    '\BO\Zmsapi\ScopeByProviderList')
    ->setName("ScopeByProviderList");

/**
 *  @swagger
 *  "/provider/{source}/":
 *      get:
 *          summary: Get a list of provider by source
 *          tags:
 *              - provider
 *          parameters:
 *              -   name: source
 *                  description: provider source like 'dldb'
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *              -   name: isAssigned
 *                  description: "get a list of provider that are already assigned to a scope"
 *                  in: query
 *                  type: boolean
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/provider.json"
 *              404:
 *                  description: "provider id for source does not exists"
 */
\App::$slim->get('/provider/{source}/',
    '\BO\Zmsapi\ProviderList')
    ->setName("ProviderList");

/**
 *  @swagger
 *  "/provider/{source}/request/{csv}/":
 *      get:
 *          summary: Get a list of provider by request numbers
 *          tags:
 *              - provider
 *          parameters:
 *              -   name: source
 *                  description: request source like 'dldb'
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: csv
 *                  required: true
 *                  description: request numbers as csv string
 *                  in: path
 *                  type: array
 *                  items:
 *                     type: string
 *                  collectionFormat: csv
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/provider.json"
 *              400:
 *                  description: "invalid tag value"
 *              404:
 *                  description: "request id for source does not exists"
 */
\App::$slim->get('/provider/{source}/request/{csv:[0-9,]{3,}}/',
    '\BO\Zmsapi\ProviderList')
    ->setName("ProviderByRequestList");

/**
 *  @swagger
 *  "/request/{source}/{id}/":
 *      get:
 *          summary: Get an request by id
 *          tags:
 *              - request
 *          parameters:
 *              -   name: source
 *                  description: request source like 'dldb'
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: id
 *                  description: request number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/request.json"
 *              404:
 *                  description: "request id for source does not exists"
 */
\App::$slim->get('/request/{source}/{id:\d{1,11}}/',
    '\BO\Zmsapi\RequestGet')
    ->setName("RequestGet");

/**
 *  @swagger
 *  "/request/{source}/provider/{id}/":
 *      get:
 *          summary: Get a list of requests by provider ID
 *          tags:
 *              - request
 *          parameters:
 *              -   name: source
 *                  description: name of source
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: id
 *                  description: number of provider
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/request.json"
 *              404:
 *                  description: "provider id does not exists"
 */
\App::$slim->get('/request/{source}/provider/{id:\d{1,11}}/',
    '\BO\Zmsapi\RequestsByProvider')
    ->setName("RequestsByProvider");

/**
 *  @swagger
 *  "/scope/":
 *      get:
 *          summary: Get a list of scopes
 *          tags:
 *              - scope
 *          parameters:
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "returns a list"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/scope.json"
 *              404:
 *                  description: "no scopes defined yet"
 */
\App::$slim->get('/scope/',
    '\BO\Zmsapi\ScopeList')
    ->setName("ScopeList");

/**
 *  @swagger
 *  "/scope/{id}/":
 *      get:
 *          summary: Get a scope
 *          tags:
 *              - scope
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/scope.json"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->get('/scope/{id:\d{1,11}}/',
    '\BO\Zmsapi\ScopeGet')
    ->setName("ScopeGet");

/**
 *  @swagger
 *  "/scope/{id}/department/":
 *      get:
 *          summary: Get a department for a scope
 *          tags:
 *              - scope
 *              - department
 *          parameters:
 *              -   name: id
 *                  description: scope id
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/department.json"
 *              404:
 *                  description: "could not find a department"
 */
\App::$slim->get('/scope/{id:\d{1,11}}/department/',
    '\BO\Zmsapi\DepartmentByScopeId')
    ->setName("DepartmentByScopeId");

/**
 *  @swagger
 *  "/scope/{id}/cluster/":
 *      get:
 *          summary: Get a cluster for a scope
 *          tags:
 *              - scope
 *              - cluster
 *          parameters:
 *              -   name: id
 *                  description: scope id
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/cluster.json"
 *              404:
 *                  description: "could not find a cluster"
 */
\App::$slim->get('/scope/{id:\d{1,11}}/cluster/',
    '\BO\Zmsapi\ClusterByScopeId')
    ->setName("ClusterByScopeId");

/**
 *  @swagger
 *  "/scope/cluster/{id}/":
 *      get:
 *          summary: Get a list of scope by cluster ID
 *          tags:
 *              - scope
 *              - cluster
 *          parameters:
 *              -   name: id
 *                  description: cluster number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/scope.json"
 *              404:
 *                  description: "cluster id does not exists"
 */
\App::$slim->get('/scope/cluster/{id:\d{1,11}}/',
    '\BO\Zmsapi\ScopeByClusterList')
    ->setName("ScopeByClusterList");

/**
 *  @swagger
 *  "/scope/{id}/availability/":
 *      get:
 *          summary: Get a list of availability entries
 *          tags:
 *              - scope
 *              - availability
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/availability.json"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->get('/scope/{id:\d{1,11}}/availability/',
    '\BO\Zmsapi\AvailabilityList')
    ->setName("AvailabilityList");

/**
 *  @swagger
 *  "/scope/{id}/process/{date}/":
 *      get:
 *          summary: Get a list of processes by scope and date
 *          tags:
 *              - scope
 *              - process
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: date
 *                  description: day in format YYYY-MM-DD
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/process.json"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->get('/scope/{id:\d{1,11}}/process/{date:\d\d\d\d-\d\d-\d\d}/',
    '\BO\Zmsapi\ProcessDay')
    ->setName("ProcessDay");

/**
 *  @swagger
 *  "/scope/{id}/emergency/":
 *      post:
 *          summary: Trigger an emergency
 *          tags:
 *              - scope
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/scope.json"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->post('/scope/{id}/emergency/',
    '\BO\Zmsapi\ScopeEmergency')
    ->setName("ScopeEmergency");

/**
 *  @swagger
 *  "/scope/{id}/emergency/":
 *      delete:
 *          summary: Cancel an emergency
 *          tags:
 *              - scope
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/scope.json"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->delete('/scope/{id}/emergency/',
    '\BO\Zmsapi\ScopeEmergencyStop')
    ->setName("ScopeEmergencyStop");

/**
 *  @swagger
 *  "/scope/{id}/emergency/respond/":
 *      post:
 *          summary: Respond to an emergency
 *          tags:
 *              - scope
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/scope.json"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->post('/scope/{id}/emergency/respond/',
    '\BO\Zmsapi\ScopeEmergencyRespond')
    ->setName("ScopeEmergencyRespond");

/**
 *  @swagger
 *  "/scope/{id}/queue/{number}/":
 *      get:
 *          summary: Get a process by queue number and scope id
 *          tags:
 *              - scope
 *              - process
 *          parameters:
 *              -   name: number
 *                  description: waitingnumber in scope for a process
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: id
 *                  description: number of scope
 *                  in: path
 *                  required: true
 *                  type: integer
 *          responses:
 *              200:
 *                  description: get a process
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/process.json"
 *              404:
 *                  description: "Could not find a process or scope not found"
 */
\App::$slim->get('/scope/{id:\d{1,4}}/queue/{number:\d{1,4}}/',
    '\BO\Zmsapi\ProcessByQueueNumber')
    ->setName("ProcessByQueueNumber");

/**
 *  @swagger
 *  "/scope/{id}/imagedata/calldisplay/":
 *      get:
 *          summary: get image data by scope id for calldisplay image
 *          tags:
 *              - scope
 *              - mimepart
 *          parameters:
 *              -   name: id
 *                  description: number of scope
 *                  in: path
 *                  required: true
 *                  type: integer
 *          responses:
 *              200:
 *                  description: get existing imagedata by scope id
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/mimepart.json"
 *              404:
 *                  description: "Could not find given scope"
 */
\App::$slim->get('/scope/{id:\d{1,4}}/imagedata/calldisplay/',
    '\BO\Zmsapi\ScopeCalldisplayImageDataGet')
    ->setName("ScopeCalldisplayImageDataGet");

/**
 *  @swagger
 *  "/scope/{id}/imagedata/calldisplay/":
 *      post:
 *          summary: upload and get image data by scope id for calldisplay image
 *          tags:
 *              - scope
 *              - mimepart
 *          parameters:
 *              -   name: id
 *                  description: number of scope
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: mimepart
 *                  description: mimepart image data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/mimepart.json"
 *          responses:
 *              200:
 *                  description: get an updated mimepart entity
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/mimepart.json"
 *              404:
 *                  description: "Could not find given scope"
 */
\App::$slim->post('/scope/{id:\d{1,4}}/imagedata/calldisplay/',
    '\BO\Zmsapi\ScopeCalldisplayImageDataUpdate')
    ->setName("ScopeCalldisplayImageDataUpdate");

/**
 *  @swagger
 *  "/scope/{id}/organisation/":
 *      get:
 *          summary: Get an organisation by scopeId.
 *          tags:
 *              - scope
 *              - organisation
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/organisation.json"
 *              404:
 *                  description: "organisation id does not exists"
 */
\App::$slim->get('/scope/{id:\d{1,4}}/organisation/',
    '\BO\Zmsapi\OrganisationByScope')
    ->setName("OrganisationByScope");

/**
 *  @swagger
 *  "/scope/{id}/queue/":
 *      get:
 *          summary: Get a waiting queue for a scope
 *          tags:
 *              - scope
 *              - queue
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/queue.json"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->get('/scope/{id:\d{1,11}}/queue/',
    '\BO\Zmsapi\ScopeQueue')
    ->setName("ScopeQueue");

/**
 *  @swagger
 *  "/scope/{id}/workstationcount/":
 *      get:
 *          summary: Get a scope with calculated workstation count.
 *          description: Calculating the workstation count requires performance, thus this is an extra api query
 *          tags:
 *              - scope
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/scope.json"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->get('/scope/{id:\d{1,4}}/workstationcount/',
    '\BO\Zmsapi\ScopeWithWorkstationCount')
    ->setName("ScopeWithWorkstationCount");

/**
 *  @swagger
 *  "/scope/{id}/":
 *      post:
 *          summary: Update a scope
 *          tags:
 *              - scope
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: scope
 *                  description: scope content
 *                  in: body
 *                  required: true
 *                  schema:
 *                      $ref: "schema/scope.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/scope.json"
 *              400:
 *                  description: "Invalid input"
 *              404:
 *                  description: "process id does not exists"
 */
\App::$slim->post('/scope/{id:\d{1,11}}/',
    '\BO\Zmsapi\ScopeUpdate')
    ->setName("ScopeUpdate");

/**
 *  @swagger
 *  "/scope/{id}/":
 *      delete:
 *          summary: Delete a scope
 *          tags:
 *              - scope
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->delete('/scope/{id:\d{1,11}}/',
    '\BO\Zmsapi\ScopeDelete')
    ->setName("ScopeDelete");

/**
 *  @swagger
 *  "/scope/{id}/waitingnumber/{hash}/":
 *      get:
 *          summary: Get a waitingNumber according to scope preferences
 *          tags:
 *              - scope
 *              - process
 *              - ticketprinter
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  required: true
 *                  in: path
 *                  type: integer
 *              -   name: hash
 *                  description: valid ticketprinter hash
 *                  required: true
 *                  in: path
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/process.json"
 *              403:
 *                  description: "hash is not valid"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->get('/scope/{id:\d{1,11}}/waitingnumber/{hash}/',
    '\BO\Zmsapi\TicketprinterWaitingnumberByScope')
    ->setName("TicketprinterWaitingnumberByScope");

/**
*  @swagger
*  "/session/{name}/{id}/":
*      get:
*          summary: Get current Session
*          tags:
*              - session
*          parameters:
*              -   name: name
*                  description: name from session (3 - 20 letters)
*                  required: true
*                  in: path
*                  type: string
*              -   name: id
*                  description: id from session (20 - 40 chars)
*                  required: true
*                  in: path
*                  type: string
*          responses:
*              200:
*                  description: get a session by id and name
*                  schema:
*                      type: object
*                      properties:
*                          meta:
*                              $ref: "schema/metaresult.json"
*                          data:
*                              $ref: "schema/session.json"
*              404:
*                  description: "Could not find any available session"
*                  schema:
*                      type: object
*                      properties:
*                          meta:
*                              $ref: "schema/metaresult.json"
*                          data:
*                              $ref: "schema/session.json"
*/
\App::$slim->get('/session/{name:[a-zA-Z]{3,20}}/{id:[a-z0-9]{8,40}}/',
    '\BO\Zmsapi\SessionGet')
    ->setName("SessionGet");

/**
 *  @swagger
 *  "/session/":
 *      post:
 *          summary: Update current Session
 *          tags:
 *              - session
 *          parameters:
 *              -   name: session
 *                  description: session content
 *                  in: body
 *                  required: true
 *                  schema:
 *                      $ref: "schema/session.json"
 *          responses:
 *              200:
 *                  description: get an updated session object
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/session.json"
 *              404:
 *                  description: "Could not find any available session"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/session.json"
 */
\App::$slim->post('/session/',
    '\BO\Zmsapi\SessionUpdate')
    ->setName("SessionUpdate");

/**
 *  @swagger
 *  "/session/{name}/{id}/":
 *      delete:
 *          summary: delete a session
 *          tags:
 *              - session
 *          parameters:
 *              -   name: name
 *                  description: name from session (3 - 20 letters)
 *                  required: true
 *                  in: path
 *                  type: string
 *              -   name: id
 *                  description: id from session (20 - 40 chars)
 *                  required: true
 *                  in: path
 *                  type: string
 *          responses:
 *              200:
 *                  description: session deleted successfully
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/session.json"
 *              404:
 *                  description: "Could not find any available session"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/session.json"
 */
\App::$slim->delete('/session/{name:[a-zA-Z]{3,20}}/{id:[a-z0-9]{20,40}}/',
    '\BO\Zmsapi\SessionDelete')
    ->setName("SessionDelete");

/**
 *  @swagger
 *  "/status/":
 *      get:
 *          summary: Get status of api
 *          tags:
 *              - status
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      $ref: "schema/status.json"
 */
\App::$slim->get('/status/',
    '\BO\Zmsapi\StatusGet')
    ->setName("StatusGet");

/**
 *  @swagger
 *  "/ticketprinter/{hash}/":
 *      get:
 *          summary: Get current Ticketprinter by hash
 *          tags:
 *              - ticketprinter
 *          parameters:
 *              -   name: hash
 *                  description: hash from ticketprinter
 *                  required: true
 *                  in: path
 *                  type: string
 *          responses:
 *              200:
 *                  description: get a ticketprinter by his hash
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/ticketprinter.json"
 *              404:
 *                  description: "Could not find any available ticketprinter"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/ticketprinter.json"
 */
\App::$slim->get('/ticketprinter/{hash:[a-z0-9]{20,40}}/',
    '\BO\Zmsapi\TicketprinterGet')
    ->setName("TicketprinterGet");

/**
 *  @swagger
 *  "/ticketprinter/":
 *      post:
 *          summary: Update ticketprinter with list of scope, cluster or link buttons
 *          tags:
 *              - ticketprinter
 *          parameters:
 *              -   name: ticketprinter
 *                  description: ticketprinter data for update
 *                  in: body
 *                  required: true
 *                  schema:
 *                      $ref: "schema/ticketprinter.json"
 *          responses:
 *              200:
 *                  description: get an updated ticketprinter object
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/ticketprinter.json"
 *              400:
 *                  description: "Invalid input"
 *              403:
 *                  description: "hash is not valid"
 *              404:
 *                  description: "Could not find any available ticketprinter"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/ticketprinter.json"
 */
\App::$slim->post('/ticketprinter/',
    '\BO\Zmsapi\Ticketprinter')
    ->setName("Ticketprinter");

/**
 *  @swagger
 *  "/useraccount/":
 *      get:
 *          summary: Get a list of useraccounts
 *          tags:
 *              - useraccount
 *          parameters:
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              type: array
 *                              items:
 *                                  $ref: "schema/useraccount.json"
 */
\App::$slim->get('/useraccount/',
    '\BO\Zmsapi\UseraccountList')
    ->setName("UseraccountList");

/**
 *  @swagger
 *  "/useraccount/{loginname}/":
 *      get:
 *          summary: Get an useraccount by loginname
 *          tags:
 *              - useraccount
 *          parameters:
 *              -   name: loginname
 *                  description: useraccount number
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/useraccount.json"
 *              404:
 *                  description: "useraccount loginname does not exists"
 */
\App::$slim->get('/useraccount/{loginname}/',
    '\BO\Zmsapi\UseraccountGet')
    ->setName("UseraccountGet");

/**
 *  @swagger
 *  "/useraccount/":
 *      post:
 *          summary: add a new useraccount
 *          tags:
 *              - useraccount
 *          parameters:
 *              -   name: useraccount
 *                  description: useraccount data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/useraccount.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/useraccount.json"
 *              404:
 *                  description: "Missing required properties in the useraccount"
 */
\App::$slim->post('/useraccount/',
    '\BO\Zmsapi\UseraccountAdd')
    ->setName("UseraccounAdd");

/**
 *  @swagger
 *  "/useraccount/{loginname}/":
 *      post:
 *          summary: Update an useraccount
 *          tags:
 *              - useraccount
 *          parameters:
 *              -   name: loginname
 *                  description: useraccount number
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: useraccount
 *                  description: useraccount data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/useraccount.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/useraccount.json"
 *              404:
 *                  description: "useraccount loginname does not exists"
 */
\App::$slim->post('/useraccount/{loginname}/',
    '\BO\Zmsapi\UseraccountUpdate')
    ->setName("UseraccountUpdate");

/**
 *  @swagger
 *  "/useraccount/{loginname}/":
 *      delete:
 *          summary: Deletes an useraccount
 *          tags:
 *              - useraccount
 *          parameters:
 *              -   name: loginname
 *                  description: useraccount number
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *              404:
 *                  description: "useraccount loginname does not exists"
 */
\App::$slim->delete('/useraccount/{loginname}/',
    '\BO\Zmsapi\UseraccountDelete')
    ->setName("UseraccountDelete");

/**
 *  @swagger
 *  "/workstation/":
 *      get:
 *          summary: Get the current workstation based on authkey
 *          tags:
 *              - workstation
 *          parameters:
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *              -   name: resolveReferences
 *                  description: "Resolve references with $ref, which might be faster on the server side. The value of the parameter is the number of iterations to resolve references"
 *                  in: query
 *                  type: integer
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/workstation.json"
 *              401:
 *                  description: "login required"
 */
\App::$slim->get('/workstation/',
    '\BO\Zmsapi\WorkstationGet')
    ->setName("WorkstationGet");

/**
 *  @swagger
 *  "/workstation/":
 *      post:
 *          summary: Update a workstation, e.g. to change the scope
 *          tags:
 *              - workstation
 *          parameters:
 *              -   name: workstation
 *                  description: workstation data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/workstation.json"
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/workstation.json"
 *              401:
 *                  description: "login required"
 */
\App::$slim->post('/workstation/',
    '\BO\Zmsapi\WorkstationUpdate')
    ->setName("WorkstationUpdate");

/**
 *  @swagger
 *  "/workstation/password/":
 *      post:
 *          operationId: WorkstationDelete
 *          summary: Change the password and/or username of a useraccount
 *          tags:
 *              - workstation
 *          parameters:
 *              -   name: useraccount
 *                  description: useraccount data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/useraccount.json"
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/useraccount.json"
 *              404:
 *                  description: "useraccount loginname does not exists"
 */
\App::$slim->post('/workstation/password/',
                  '\BO\Zmsapi\WorkstationPassword')
    ->setName('WorkstationPassword');

/**
 *  @swagger
 *  "/workstation/{loginname}/":
 *      post:
 *          summary: Create a workstation for an username, used to login
 *          tags:
 *              - workstation
 *          parameters:
 *              -   name: loginname
 *                  description: useraccount identifier, usually the unique loginname
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: useraccount
 *                  description: useraccount data to update
 *                  required: true
 *                  in: body
 *                  schema:
 *                      $ref: "schema/useraccount.json"
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/workstation.json"
 *              404:
 *                  description: "useraccount loginname does not exists"
 */
\App::$slim->post('/workstation/{loginname}/',
    '\BO\Zmsapi\WorkstationLogin')
    ->setName("WorkstationLogin");

/**
 *  @swagger
 *  "/workstation/{loginname}/":
 *      delete:
 *          operationId: WorkstationDelete
 *          summary: Logout a user and delete his workstation entry
 *          tags:
 *              - workstation
 *          parameters:
 *              -   name: loginname
 *                  description: useraccount number
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: X-Authkey
 *                  description: authentication key to identify user for testing access rights
 *                  in: header
 *                  type: string
 *          responses:
 *              200:
 *                  description: "success"
 *                  schema:
 *                      type: object
 *                      properties:
 *                          meta:
 *                              $ref: "schema/metaresult.json"
 *                          data:
 *                              $ref: "schema/workstation.json"
 *              404:
 *                  description: "useraccount loginname does not exists"
 */
\App::$slim->delete('/workstation/{loginname}/',
    '\BO\Zmsapi\WorkstationDelete')
    ->setName("WorkstationDelete");


/* ---------------------------------------------------------------------------
 * maintenance
 * -------------------------------------------------------------------------*/

\App::$slim->get('/healthcheck/',
                 '\BO\Zmsapi\Healthcheck')
    ->setName("healthcheck");

\App::$slim->getContainer()->offsetSet('notFoundHandler', function ($container) {
    return function (RequestInterface $request, ResponseInterface $response) {
        $message = \BO\Zmsapi\Response\Message::create($request);
        $message->meta->error = true;
        $message->meta->message = "Could not find a resource with the given URL";
        $response = \BO\Slim\Render::withLastModified($response, time(), '0');
        return \BO\Slim\Render::withJson($response, $message, 404);
    };
});

\App::$slim->getContainer()->offsetSet('errorHandler', function ($container) {
    return function (RequestInterface $request, ResponseInterface $response, \Exception $exception) {
        $message = \BO\Zmsapi\Response\Message::create($request);
        $message->meta->error = true;
        $message->meta->message = $exception->getMessage();
        $message->meta->exception = get_class($exception);
        $message->meta->trace = $exception->getTrace();
        if (isset($exception->data)) {
            $message->data = $exception->data;
        }
        $response = \BO\Slim\Render::withLastModified($response, time(), '0');
        $status = 500;
        if ($exception->getCode() >= 200) {
            $status = $exception->getcode();
        }
        if ($exception->getCode() >= 500 || !$exception->getCode()) {
            \App::$log->critical(
                "PHP Fatal Exception: "
                . " in " . $exception->getFile() . " +" . $exception->getLine()
                . " -> " . $exception->getMessage()
            );
        }
        return \BO\Slim\Render::withJson($response, $message, $status);
    };
});
