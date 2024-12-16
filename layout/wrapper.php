<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.soap@1.7.3/jquery.soap.min.js"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
<?php require_once "header.php"; ?>

<div class="container mx-auto">
    <?php echo $pageContent ?? ''; ?>
</div>
<div id="rest2ApiUrl" data-api-url="<?php echo REST2_API_URL; ?>" style="display:none;"></div>
<div id="restApiUrl" data-api-url="<?php echo REST_API_URL; ?>" style="display:none;"></div>
</body>
</html>
