<?php

Route::get('/', 'HomeController@index')->name('home');
Route::get('/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::get('/change_locale/{local}', 'HomeController@changeLocale');
Route::get('/contact-us', 'HomeController@contactUs');
Route::post('/contact-us', 'HomeController@sendContactEmail');

Route::get('/privacy-policy', 'HomeController@privacyPolicy');
Route::get('/terms-conditions', 'HomeController@termsConditions');

// Route::get('facebook', function () {
//     return view('facebook');
// });

Route::get('auth/facebook', 'Auth\FacebookController@redirectToFacebook');
Route::get('auth/facebook/callback', 'Auth\FacebookController@handleFacebookCallback');

Auth::routes(['verify' => true]);

Route::post('user/appeal', 'UsersController@appeal');

Route::group([
    'middleware' => ['auth'],
], function () {

    Route::get('/news', 'NewsController@index');
    Route::get('/events', 'EventsController@index');
    Route::get('/events/all', 'EventsController@all');
    Route::get('/events/{slug}', 'EventsController@show');
    Route::get('/event/{slug}', 'EventsController@getEvent');

    Route::get('/communities', 'CommunitiesController@index');
    Route::get('/communities/all', 'CommunitiesController@all');
    Route::get('/communities/{slug}', 'CommunitiesController@show');
    Route::get('/community/{slug}', 'CommunitiesController@getCommunity');
    Route::post('/community/{slug}/join', 'CommunitiesController@joinCommunity');
    Route::post('/community/{slug}/leave', 'CommunitiesController@leaveCommunity');

    Route::get('/friends', 'FriendsController@index');
    Route::get('/friends/all', 'FriendsController@all');

    Route::get('/profiles', 'ProfileController@index');
    Route::post('/profiles', 'ProfileController@profiles');

    Route::get('/search-page', 'SearchController@index');

    Route::get('/search/all', 'SearchController@all');

    Route::post('/search/all', 'SearchController@all');
    Route::get('/search/communities', 'SearchController@communities');
    Route::post('/search/communities', 'SearchController@communities');
    Route::post('/communities/popular', 'CommunitiesController@getPopularCommunities');
    // Route::post('/communities/popular', 'SearchController@communities');


    Route::get('/search/posts', 'SearchController@posts');
    Route::post('/search/posts', 'SearchController@posts');


    Route::get('/search/events', 'SearchController@events');
    Route::post('/search/events', 'SearchController@events');

    Route::get('/search/profiles', 'SearchController@profiles');
    Route::post('/search/profiles', 'SearchController@profiles');

    Route::post('/search/interests', 'SearchController@interests');
    Route::post('/search/locations', 'SearchController@locations');
    Route::post('/search/educations', 'SearchController@educations');

    Route::get('/chat', 'ChatController@index');
    Route::get('/chat/{username}', 'ChatController@index');

    Route::get('/profiles/{username}', 'ProfileController@show');

    Route::get('/user/{username}/settings', 'SettingsController@index');
    Route::put('/user/{username}/settings', 'ProfileController@update');
    

    Route::put('/user/settings/updateEmail', 'SettingsController@updateEmail');
    Route::put('/user/settings/updatePassword', 'SettingsController@updatePassword');
    Route::put('/user/settings/updateName', 'SettingsController@updateName');
    Route::put('/user/settings/updateLocation', 'LocationsController@updateOrCreateLocation');
    Route::put('/user/settings/updateLanguage', 'SettingsController@updateOrCreateLanguages');
    Route::put('/user/settings/updateEducation', 'SettingsController@updateOrCreateEducations');
    Route::put('/user/settings/updateGender', 'SettingsController@updateGender');
    Route::put('/user/settings/updateTimezone', 'SettingsController@updateTimezone');
    Route::put('/user/settings/updateBio', 'SettingsController@updateBio');

    Route::post('/user/settings/updateImage', 'SettingsController@updateImage');
    Route::post('/user/settings/deactivate', 'SettingsController@deactivateAccount');
    Route::post('/user/settings/activate', 'SettingsController@activateAccount');
    Route::post('/user/settings/deleteAccount', 'SettingsController@deleteAccount');

    Route::post('/user/settings/updateLocationBirthdayEducationPrivacy', 'SettingsController@updateLocationBirthdayEducationPrivacy');
    // Route::post('/user/settings/updateLBirthdayPrivacy', 'SettingsController@updateLBirthdayPrivacy');
    // Route::post('/user/settings/updateEducationrivacy', 'SettingsController@updateEducationrivacy');

    Route::get('/user/settings/blockedProfiles', 'ProfileController@getBlockedProfiles');

    Route::put('/user/settings/updateSearchabilityPrivacy', 'PrivaciesController@updateSearchabilityPrivacy');
    Route::put('/user/settings/updateMyFriendsPrivacy', 'PrivaciesController@updateMyFriendsPrivacy');
    Route::put('/user/settings/updateMyEventsPrivacy', 'PrivaciesController@updateMyEventsPrivacy');
    Route::put('/user/settings/updateMyCommunitiesPrivacy', 'PrivaciesController@updateMyCommunitiesPrivacy');

    Route::get('/profile/{username}', 'FriendsController@getFriends');
    Route::get('/profile/{username}/myCommunities', 'CommunitiesController@myCommunities');

    Route::post('/profile/{username}', 'ProfileController@requestFriendship');

    Route::post('/profile/{username}/chat/get', 'ConversationsController@getConversations');
    Route::post('/profile/{username}/chat', 'ConversationsController@createConversation');
    Route::post('/profile/{username}/chat/createConversation', 'ConversationsController@createConversation');


    Route::post('/profile/{username}/chat/{id}', 'ConversationsController@getMessages');

    Route::post('/profile/{username}/chat/{conversation}/send', 'ConversationsController@sendMessage');
    Route::patch('/profile/{username}/chat/{conversation}/send', 'ConversationsController@sendMessage');

    Route::post('/profile/{username}/chat/{conversationId}/markAsRead', 'ConversationsController@markConversationAsRead');

    Route::post('/profile/{username}/chat/{conversation}/notify', 'ConversationsController@sendNotification');


    Route::post('/profile/{username}/like', 'LikesController@like');
    Route::post('/profile/{username}/removeLike', 'LikesController@removeLike');

    Route::get('/profile/{username}/activity', 'ActivitiesController@get');

    Route::post('/events/create', 'EventsController@store');
    Route::post('/events/invite', 'EventsController@inviteProfiles');

    Route::post('/event/{slug}/join', 'EventsController@joinEvent');

    Route::post('/event/{slug}/conversation/get', 'ConversationsController@getConversation');
    Route::post('/event/{slug}/chat/{id}/send', 'ConversationsController@sendEventMessage');
    Route::patch('/event/{slug}/chat/{id}/send', 'ConversationsController@sendEventMessage');

    Route::post('/events/requestInvitation', 'RequestsController@requestInvitation');
    Route::post('/communities/requestInvitation', 'RequestsController@requestInvitation');

    Route::patch('/event/{slug}/update', 'EventsController@updateEvent');
    Route::post('/event/{slug}/post', 'EventsController@storePostMessage');

    Route::post('/event/{slug}/leave', 'EventsController@leaveEvent');

    Route::get('/profile/{username}/events', 'EventsController@myEvents');

    Route::post('/communities/create', 'CommunitiesController@store');
    Route::post('/communities/invite', 'CommunitiesController@inviteProfiles');
    Route::patch('/community/{slug}/update', 'CommunitiesController@updateCommunity');
    Route::post('/community/{slug}/makeAdmin', 'CommunitiesController@makeAdmin');
    Route::post('/community/{slug}/removeAdmin', 'CommunitiesController@removeAdmin');

    Route::post('/community/{slug}/post', 'CommunitiesController@storePostMessage');

    Route::get('/community/{slug}/upcomingEvents', 'CommunitiesController@upcomingEvents');

    Route::get('/invitations', 'InvitationsController@get');
    Route::post('/invitations/accept', 'InvitationsController@acceptInvitation');
    Route::post('/invitations/refuse', 'InvitationsController@refuseInvitation');
    Route::post('/invitations/getInvitations', 'InvitationsController@getInvitationsForResource');

    Route::get('/requests', 'InvitationsController@getFriendRequests');
    Route::post('/requests/accept', 'InvitationsController@acceptFriendRequest');
    Route::post('/requests/refuse', 'InvitationsController@refuseFriendRequest');
    Route::post('/requests/unfriend', 'InvitationsController@unfriend');
    Route::post('/requests/cancelFriendRequest', 'InvitationsController@cancelFriendRequest');

    Route::get('/getRequests', 'RequestsController@get');
    Route::post('/resourceRequests/accept', 'RequestsController@acceptRequest');
    Route::post('/resourceRequests/refuse', 'RequestsController@refuseRequest');

    Route::post('/profile/{username}/block', 'ProfileController@blockProfile');
    Route::post('/profile/{username}/unblock', 'ProfileController@unblockProfile');

    // Route::get('/user/logout', 'Auth\LoginController@logout');
    // Route::get('/user', 'Auth\LoginController@user');

    Route::get('/hallway', 'ConferencesController@hallway')->name('hallway');
    Route::get('/hallway/{slug}', 'ConferencesController@eventHallway')->name('eventHallway');
    Route::post('/hallway/{username}/access', 'ConferencesController@generateAccessToken');

    Route::post('/hallway/{slug}', 'ConferencesController@getCircle');
    Route::post('/hallway/{slug}/enter', 'ConferencesController@enterConference');
    Route::post('/hallway/{slug}/leave', 'ConferencesController@leaveConference');
    Route::post('/hallway/{slug}/updateHallway', 'ConferencesController@updateHallway');


    Route::post('/reports/create', 'ReportsController@store');

    Route::get('/interests', 'InterestsController@get');
    Route::get('/timezones', 'TimezonesController@get');
    Route::get('/genders', 'GendersController@get');

    Route::get('/languages', 'LanguagesController@index');
    Route::get('/educations', 'EducationsController@index');

    Route::get('/privacies', 'PrivaciesController@getPrivacies');
    Route::get('/relationshipPrivacies', 'PrivaciesController@getRelationshipPrivacies');
});

Route::post('/user/register', 'Auth\RegisterController@register');
Route::post('/user/sendVerificationCode', 'Auth\RegisterController@sendVerificationCode');
Route::post('/user/verifyCode', 'Auth\RegisterController@verifyCode');

Route::post('/user/login', 'Auth\LoginController@login')->name('login');

Route::get('/user/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/countries', 'LocationsController@get');
