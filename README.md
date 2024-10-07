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
