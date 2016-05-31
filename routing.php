<?php
// @codingStandardsIgnoreFile
/**
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

/* ---------------------------------------------------------------------------
 * html, basic routes
 * -------------------------------------------------------------------------*/

\App::$slim->get('/', '\BO\Zmsadmin\Index')->setName("pagesindex");

\App::$slim->get('/login/', '\BO\Zmsadmin\Index')->setName("login");
    
\App::$slim->get('/workstation/process/{id:\d+}/precall/', '\BO\Zmsadmin\WorkstationClientPreCall')
    ->setName("workstationClientPreCall");

\App::$slim->get('/workstation/process/{id:\d+}/called/', '\BO\Zmsadmin\WorkstationClientCalled')
    ->setName("workstationClientCalled");

\App::$slim->get('/workstation/process/{id:\d+}/processed', '\BO\Zmsadmin\WorkstationClientProcessed')
    ->setName("workstationClientProcessed");

\App::$slim->get('/workstation/process/{id:\d+}/', '\BO\Zmsadmin\WorkstationClientActive')
    ->setName("workstationClientActive");
    
\App::$slim->map(['GET', 'POST'], '/workstation/', '\BO\Zmsadmin\Workstation')
    ->setName("workstation");

\App::$slim->get('/counter/', '\BO\Zmsadmin\Counter')
    ->setName("counter");

\App::$slim->get('/scope/', '\BO\Zmsadmin\Scope')
    ->setName("scope");

\App::$slim->get('/scope/{id:\d+}/pickup/', '\BO\Zmsadmin\Pickup')
    ->setName("pickup");

\App::$slim->map(['GET', 'POST'], '/scope{id/pickup/handheld/', '\BO\Zmsadmin\PickupHandheld')
    ->setName("pickup_handheld");
    
\App::$slim->get('/scope/{id:\d+}/pickup/keyboard/', '\BO\Zmsadmin\PickupKeyboard')
    ->setName("pickup_keyboard");

\App::$slim->get('/scope/{scope_id:\d+}/availability/day/', '\BO\Zmsadmin\ScopeAvailabilityDay')
    ->setName("scopeavailabilityday");

\App::$slim->get('/cluster/', '\BO\Zmsadmin\Cluster')
    ->setName("cluster");

\App::$slim->get('/department/', '\BO\Zmsadmin\Department')
    ->setName("department");

\App::$slim->get('/organisation/', '\BO\Zmsadmin\Organisation')
    ->setName("organisation");

\App::$slim->get('/owner/', '\BO\Zmsadmin\Owner')
    ->setName("owner");

\App::$slim->get('/owner/{id:\d+}/', '\BO\Zmsadmin\OwnerEdit')
    ->setName("ownerEdit");
    
\App::$slim->get('/availability/day/', '\BO\Zmsadmin\Availability')
    ->setName("availability_day");

\App::$slim->get('/availability/month/', '\BO\Zmsadmin\AvailabilityMonth')
    ->setName("availability_month");

\App::$slim->get('/calendar{year/kw{weeknr/', '\BO\Zmsadmin\CalendarWeek')
    ->setName("calendar_week");

\App::$slim->get('/profile/', '\BO\Zmsadmin\Profile')
    ->setName("profile");

\App::$slim->get('/useraccount/', '\BO\Zmsadmin\Useraccount')
    ->setName("useraccount");

\App::$slim->get('/department/{id:\d+}/useraccount/', '\BO\Zmsadmin\UseraccountByDepartment')
    ->setName("useraccountByDepartment");

\App::$slim->get('/useraccount/{id:\d+}/', '\BO\Zmsadmin\UseraccountEdit')
    ->setName("useraccountEdit");

\App::$slim->get('/calldisplay/', '\BO\Zmsadmin\Calldisplay')
    ->setName("calldisplay");

\App::$slim->get('/scope/ticketprinter/', '\BO\Zmsadmin\TicketprinterConfig')
    ->setName("ticketprinter");
    
\App::$slim->get('/scope/{id:\d+}/ticketprinter/', '\BO\Zmsadmin\TicketprinterStatusByScope')
    ->setName("ticketprinterStatusByScope");

\App::$slim->get('/notification/', '\BO\Zmsadmin\Notification')
    ->setName("notification");

\App::$slim->get('/links/', '\BO\Zmsadmin\Links')
    ->setName("links");

\App::$slim->get('/search/', '\BO\Zmsadmin\Search')
    ->setName("search");

\App::$slim->get('/dayoff/', '\BO\Zmsadmin\Dayoff')
    ->setName("dayoff");

\App::$slim->get('/dayoff/{year:\d+}/', '\BO\Zmsadmin\DayoffByYear')
    ->setName("dayoffByYear");

\App::$slim->get('/dayoff/{year:\d+}/{id:\d+}/', '\BO\Zmsadmin\DayoffEdit')
    ->setName("dayoffEdit");
    
\App::$slim->get('/department/{id:\d+}/dayoff/', '\BO\Zmsadmin\DayoffByDepartment')
    ->setName("dayoffByDepartment");

\App::$slim->get('/department/{id:\d+}/dayoff/{year:\d+}/', '\BO\Zmsadmin\DayoffByDepartmentAndYear')
    ->setName("dayoffByDepartmentAndYear");

\App::$slim->get('/testpage/', '\BO\Zmsadmin\Testpage')
    ->setName("testpage");

//\App::$slim->get('/dienstleistung{service_id:\d+}', '\BO\D115Mandant\Controller\ServiceDetail')
//    ->setName("servicedetail");

/* ---------------------------------------------------------------------------
 * externals
 * -------------------------------------------------------------------------*/

// external link to stadplan
\App::$slim->get('http://www.Berlin.de/stadtplan/', function () {})
    ->setName("citymap");

/* ---------------------------------------------------------------------------
 * maintenance
 * -------------------------------------------------------------------------*/

\App::$slim->get('/healthcheck/', '\BO\Zmsappointment\Healthcheck')
    ->setName("healthcheck");    

\App::$slim->getContainer()->notFoundHandler = function() {
    return function () {
        return \BO\Slim\Render::html('404.twig');
    };
};

\App::$slim->getContainer()->errorHandler = function() {
    return function (\Exception $exception) {
        return \BO\Zmsappointment\Helper\Render::error($exception);
    };
};
