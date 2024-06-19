# Video Uploader

## Overview

This project allows users to upload videos from your website directly to their TikTok and YouTube accounts.

## Features

1. Display a list of videos.
2. User authentication with TikTok and YouTube.
3. Upload videos to the user's TikTok and YouTube accounts.

## Steps to Implement

### Display a List of Videos

Ensure each video has a unique identifier for reference.

### TikTok Integration

Users need to log in to their TikTok account and grant permissions for your app to post on their behalf. TikTok uses OAuth 2.0 for authentication.

### YouTube Integration

Users need to log in to their Google account and grant permissions for your app to post on YouTube. YouTube also uses OAuth 2.0 for authentication.

## Detailed Implementation

### 1. Setup TikTok Authentication

First, register your application with TikTok to get the necessary `client_id` and `client_secret`.

#### TikTok OAuth 2.0 Flow:

1. Redirect user to TikTok's authorization endpoint.
2. Handle the callback and get the authorization code.
3. Exchange the authorization code for an access token.
4. Use the access token to upload the video.

```php
// Step 1: Redirect to TikTok's OAuth 2.0 Authorization Endpoint
$client_id = 'YOUR_TIKTOK_CLIENT_ID';
$redirect_uri = 'YOUR_REDIRECT_URI';
$scope = 'video.upload';

$auth_url = "https://open-api.tiktok.com/platform/oauth/connect?client_key={$client_id}&scope={$scope}&response_type=code&redirect_uri={$redirect_uri}";
header('Location: ' . $auth_url);
exit;

// Step 2: Handle the callback and get the authorization code
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Step 3: Exchange the authorization code for an access token
    $token_url = 'https://open-api.tiktok.com/oauth/access_token';
    $params = [
        'client_key' => $client_id,
        'client_secret' => 'YOUR_TIKTOK_CLIENT_SECRET',
        'code' => $code,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $token_info = json_decode($response, true);
    $access_token = $token_info['data']['access_token'];

    // Step 4: Use the access token to upload the video
    // Assuming $video_path is the path to the video file
    $upload_url = 'https://open-api.tiktok.com/video/upload/';
    $video_path = 'path_to_your_video.mp4';

    $post_data = [
        'access_token' => $access_token,
        'video' => new CURLFile($video_path)
    ];

    $ch = curl_init($upload_url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $upload_response = curl_exec($ch);
    curl_close($ch);

    // Handle the response from TikTok
    $upload_info = json_decode($upload_response, true);
    print_r($upload_info);
}
