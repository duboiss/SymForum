## Installation
### Step 1 - Download project
Clone project and move inside.
```sh
$ git clone https://github.com/DuboisS/symforum.git
$ cd symforum
```

### Step 2 - Containers and volume
Install docker containers and database volume.
It may take a little time the first time (pull images).
```sh
$ docker-compose up -d
```

### Step 3 - Project
Install dependencies, database schema, fixtures and build assets.
```sh
$ make install
```

## Step 3 - Enjoy !
Open `https://symforum.localhost` in your favorite browser.
