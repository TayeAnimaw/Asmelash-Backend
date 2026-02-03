<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Asmelash Construction</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .topbar {
            background-color: #894F30;
            padding: 15px 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .topbar .version {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <div class="topbar">
        <h1>Asmelash Construction API</h1>
        <span class="version">v1.0.0</span>
    </div>
    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js"></script>
    <script>
        window.onload = function () {
            SwaggerUIBundle({
                url: '/api-docs.json',
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIBundle.SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                initOAuth: {
                    clientId: 'swagger-ui',
                    scopes: []
                }
            });
        };
    </script>
</body>

</html>