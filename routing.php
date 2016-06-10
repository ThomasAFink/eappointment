<?php
// @codingStandardsIgnoreFile
/**
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

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
 *          description: Get an availability by id
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
 *  "/availability/{id}/":
 *      post:
 *          description: Update an availability
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
 *          description: Deletes an availability
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
*          description: Get a list of available days for appointments
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
*          description: Get preferences for a calldisplay
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
*                  description: get an updated calendar objects with updated days list
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
*          description: Get queue for a calldisplay
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
 *          description: Get an cluster by id
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
 *          description: Update an cluster
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
 *          description: Deletes an cluster
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
 *          description: Get a waiting queue for a cluster
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
 *  "/dayoff/{year}/":
 *      get:
 *          description: Get a list of common free days for a given year
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
 *          description: Update list of common free days
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
 *          description: Get a list of organisations
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
 *          description: Get an department by id
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
 *          description: Update an department
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
 *  "/department/":
 *      post:
 *          description: Add a new department
 *          parameters:
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
\App::$slim->post('/department/',
    '\BO\Zmsapi\DepartmentAdd')
    ->setName("DepartmentAdd");

/**
 *  @swagger
 *  "/department/{id}/":
 *      delete:
 *          description: Deletes an department
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
 *  "/mails/":
 *      get:
 *          description: get a list of mails in the send queue
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
 *          description: Add a mail to the send queue
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
 *          description: delete a mail in the send queue
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
 *          description: get a list of notifications in the send queue
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
 *          description: Add a notification to the send queue
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
 *          description: delete a notification in the send queue
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
 *          description: Get a list of owners
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
 *          description: Get an owner by id
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
 *          description: Update an owner
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
 *  "/owner/{id}/":
 *      delete:
 *          description: Deletes an owner
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
 *  "/organisation/":
 *      get:
 *          description: Get a list of organisations
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
 *          description: Get an organisation by id
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
 *          description: Update an organisation
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
 *          description: Deletes an organisation
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
 *          description: Get a hash to identify a ticketprinter. Usually a browser requests a hash once and stores it in a cookie.
 *          parameters:
 *              -   name: id
 *                  description: organisation number
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
 *                              $ref: "schema/ticketprinter.json"
 *              404:
 *                  description: "organisation id does not exists"
 */
\App::$slim->get('/organisation/{id:\d{1,11}}/hash/',
    '\BO\Zmsapi\OrganisationHash')
    ->setName("OrganisationHash");

/**
 *  @swagger
 *  "/process/{id}/{authKey}/":
 *      get:
 *          description: Get a process
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
 *          description: Get an ICS-File for a process
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
 *          description: Update a process but does not send any mails or notifications on status changes
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
 *  "/process/{id}/{authKey}/":
 *      delete:
 *          description: Deletes a process but does not send any mails or notifications
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
 *          description: Get a list of free processes for a given day
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
 *          description: Get a list of reserved processes
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
 *          description: Try to reserve the appointments in a process
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
 *              403:
 *                  description: "authkey does not match"
 *              404:
 *                  description: "Could not find any processes, returns empty list"
 */
\App::$slim->post('/process/status/reserved/',
    '\BO\Zmsapi\ProcessReserve')
    ->setName("ProcessReserve");

/**
 *  @swagger
 *  "/process/status/confirmed/":
 *      post:
 *          description: Try to confirm a process, changes status from reservered to confirmed
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
 *          description: Get an provider by id
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
 *  "/provider/{source}/request/{csv}/":
 *      get:
 *          description: Get a list of provider by request numbers
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
 *          description: Get an request by id
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
 *  "/scope/":
 *      post:
 *          description: Add a new scope
 *          parameters:
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
\App::$slim->post('/scope/',
    '\BO\Zmsapi\ScopeAdd')
    ->setName("ScopeAdd");

/**
 *  @swagger
 *  "/scope/":
 *      get:
 *          description: Get a list of scopes
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
 *          description: Get a scope
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
 *  "/scope/provider/{id}/":
 *      get:
 *          description: Get a list of scope by provider ID
 *          parameters:
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
 *                              $ref: "schema/scope.json"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->get('/scope/provider/{id}/',
    '\BO\Zmsapi\ScopeByProviderList:\d{1,11}')
    ->setName("ScopeByProviderList");

/**
 *  @swagger
 *  "/scope/cluster/{id}/":
 *      get:
 *          description: Get a list of scope by cluster ID
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
 *                              $ref: "schema/scope.json"
 *              404:
 *                  description: "scope id does not exists"
 */
\App::$slim->get('/scope/cluster/{id:\d{1,11}}/',
    '\BO\Zmsapi\ScopeByClusterList')
    ->setName("ScopeByClusterList");

/**
 *  @swagger
 *  "/scope/{id}/availability/":
 *      get:
 *          description: Get a list of availability entries
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
 *  "/scope/{id}/queue/":
 *      get:
 *          description: Get a waiting queue for a scope
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
 *  "/scope/{id}/":
 *      post:
 *          description: Update a scope
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
 *          description: Delete a scope
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
*  "/session/{name}/{id}/":
*      get:
*          description: Get current Session
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
\App::$slim->get('/session/{name:[a-zA-Z]{3,20}}/{id:[a-z0-9]{20,40}}/',
    '\BO\Zmsapi\SessionGet')
    ->setName("SessionGet");

/**
 *  @swagger
 *  "/session/":
 *      post:
 *          description: Update current Session
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
 *          description: delete a session
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
 *          description: Get status of api
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
 *  "/ticketprinter/{id}/waitingnumber/":
 *      get:
 *          description: Get a waitingNumber according to scope preferences
 *          parameters:
 *              -   name: id
 *                  description: scope number
 *                  in: path
 *                  required: true
 *                  type: integer
 *              -   name: ticketprinter
 *                  description: ticketprinter data, a valid hash is required
 *                  in: body
 *                  required: true
 *                  schema:
 *                      $ref: "schema/ticketprinter.json"
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
 *                  description: "ticketprinter id does not exists"
 */
\App::$slim->get('/ticketprinter/{id:\d{1,11}}/waitingnumber/',
    '\BO\Zmsapi\TicketprinterWaitingnumber')
    ->setName("TicketprinterWaitingnumber:");

/**
 *  @swagger
 *  "/useraccount/":
 *      get:
 *          description: Get a list of useraccounts
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
 *          description: Get an useraccount by loginname
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
 *  "/useraccount/{loginname}/":
 *      post:
 *          description: Update an useraccount
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
 *          description: Deletes an useraccount
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
 *          description: Get the current workstation based on authkey
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
 *          description: Update a workstation, e.g. to change the scope
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
 *  "/workstation/{loginname}/":
 *      post:
 *          description: Create a workstation for an username, used to login
 *          parameters:
 *              -   name: loginname
 *                  description: useraccount identifier, usually the unique loginname
 *                  in: path
 *                  required: true
 *                  type: string
 *              -   name: password
 *                  description: password to verify user
 *                  in: formData
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
 *          description: Logout a user and delete his workstation entry
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

\App::$slim->getContainer()->notFoundHandler = function() {
    return function () {
        $message = \BO\Zmsapi\Response\Message::create();
        $message->meta->error = true;
        $message->meta->message = "Could not find a resource with the given URL";
        \BO\Slim\Render::lastModified(time(), '0');
        return \BO\Slim\Render::json($message, 404);
    };
};

\App::$slim->getContainer()->errorHandler = function() {
    return function (\Exception $exception) {
        $message = \BO\Zmsapi\Response\Message::create();
        $message->meta->error = true;
        $message->meta->message = $exception->getMessage();
        $message->meta->trace = $exception->getTrace();
        \BO\Slim\Render::lastModified(time(), '0');
        return \BO\Slim\Render::json($message, 500);
    };
};
