# Virtual Staging

## Prerequisites

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

## Running the Project

1. From projects root run:

    ```bash
    docker-compose up
    ```

    > Note: To start in detached mode run `docker-compose up -d`

2. Access the project at [http://localhost:8080](http://localhost:8080)

    > Note: To make REST API available set permalinks to `Post name` in WordPress settings.

### Stopping the Project

To stop the project:

```bash
docker-compose down
```

## Environment Configuration

The environment configuration is done through the `.env` file located in the root directory of the plugin. The following environment variables are available:

```dotenv
VIRTUAL_STAGING_API_URL: The URL of the Virtual Staging API.
VIRTUAL_STAGING_API_KEY: The API key for the Virtual Staging API.
DEV_MODE: Set to `true` to bypass authorization for the plugin's API endpoints during development. For production, ensure this is set to `false` to enforce security.
```

## Form Shortcodes

The plugin provides two shortcodes to embed upload form and main page the forms in the WordPress site:

Add the shortcode where you want your template to appear:

```php
[vsai_template name="upload_form"]
```

If you need to pass data:

```php
[vsai_template name="main_page" data='{"api_result": "Sample API Result"}']
```
