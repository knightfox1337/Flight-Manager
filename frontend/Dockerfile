FROM node:lts as base

# working directory
WORKDIR /app

# copy package.json files
COPY package.json /app/package.json
COPY package-lock.json /app/package-lock.json

# create build stage
FROM base as build

# install dependencies
RUN npm install --silent

# add app files
COPY . /app

# build app
RUN npm run build

# copy only production files over to final build
# FROM base as release
# RUN rm -rf /app/package.json && rm -rf /app/package-lock.json
# COPY --from=build /app/package.json /app/package.json
# COPY --from=build /app/build /app/build
# COPY --from=build /app/server.js /app/server.js

# start app
CMD ["npm", "start"]