FROM node:22-alpine

WORKDIR /usr/src/app
COPY ./js ./

RUN npm install -g npm@11.1.0 \
  && npm install

CMD node test.js


