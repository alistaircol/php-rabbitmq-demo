#!/usr/bin/env bash
curl --user guest:guest \
    http://localhost:15672/api/definitions \
    | jq . > definitions.json

docker-compose restart
