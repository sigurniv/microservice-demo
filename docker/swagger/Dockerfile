FROM swaggerapi/swagger-ui

COPY swagger.json /app/swagger.json

ARG APP_PORT=8091

RUN sed -i -e "s/APP_PORT/$APP_PORT/g" /app/swagger.json

