
<!-- PROJECT LOGO -->
<br />
<div align="center">

<h3 align="center">MA Server - Working Title</h3>
  <p align="center">
    <i>A server for Generation 3 Pokémon RomHacks using the Mobile Adapter.</i>
    <br />
    <a href="https://gts.paccypad.com">View Demo</a>
    &middot;
    <a href="https://github.com/Artrios/MA_Server/issues/new?labels=bug&template=bug-report---.md">Report Bug</a>
    &middot;
    <a href="https://github.com/Artrios/MA_Server/issues/new?labels=enhancement&template=feature-request---.md">Request Feature</a>
  </p>
</div>


<!-- ABOUT THE PROJECT -->
## About The Project

The Mobile Adapter Server is a service for Generation 3 Pokémon RomHacks using the [Mobile Adapter GB](https://en.wikipedia.org/wiki/Mobile_Adapter_GB). The server will initially provide a Global Trade System (GTS) service but will expand to other features.
<p align="right">(<a href="#readme-top">back to top</a>)</p>



### Built With

* [![Symfony](https://img.shields.io/badge/Symfony-black?logo=symfony)](#)
* [![PHP](https://img.shields.io/badge/php-%23777BB4.svg?&logo=php&logoColor=white)](#)
* [![Postgres](https://img.shields.io/badge/Postgres-%23316192.svg?logo=postgresql&logoColor=white)](#)
* [![Docker](https://img.shields.io/badge/Docker-2496ED?logo=docker&logoColor=fff)](#)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

This is an example of how you may give instructions on setting up your project locally.
To get a local copy up and running follow these simple example steps.

### Prerequisites

* Docker

### Installation

1. Clone the repo
   ```sh
   git clone https://github.com/Artrios/MA_Server.git
   ```
2. Modify `frankenphp/Caddyfile` to change `example.com` to the domain you'll be using

3. Start up the docker container
   ```sh
   docker compose up
   ```
4. Set up the database tables and validate (your docker image name may be different).
   ```sh
   docker exec ma_server-php-1 php bin/console doctrine:schema:create
   docker exec ma_server-php-1 php bin/console doctrine:schema:validate
   ```

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

This project sets up a Global Trade Station (GTS) server that modified Generation 3 Pokemon games can connect to. The server is still in its early stages and as such there's only a demo RomHacks that can connect to it:

* [Demo Hack](https://github.com/Artrios/pokeemerald/tree/expansion-internet)

In addition to the game either [this fork of the mGBA emulator](https://github.com/Wit-MKW/mgba) is required, or a Raspberry Pi Pico to [emulate the Mobile Adapter GB](https://github.com/zenaror/PicoAdapterGB).

This projects follows the Generation 4/5 implementation of the GTS in regards to available API endpoints, query/response formats and authentication. It also provides a simple frontend to show recent Pokemon deposits.

Read more of the docs [here](docs/README.md).

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- ROADMAP -->
## Future Plans
I have a lot of ideas that I hopefully will get to.

Features to provide clients:
- GTS
- Trading
    - Through Friend Code
    - Through Room Number
    - Wonder Trade
- Battle
    - Through Friend Code
    - Through Room Number
    - Ranked Batles
    - Random Casual Battles
- Friend System
- Mystery Gift/Wonder Cards
- Box System
- Secret Bases
    - Upload your Secret Base and share with friends

Server Features:
- Add support for vanilla Gen 3 games
- Add a compatability layer to allow trades between romhacks and vanilla games
    - Using PkHeX Pokemon could be coverted between games
- Add a legitimacy checker
    - Again using PkHeX
    - Only allow Pokemon to be traded if it passes check(?)
- Show trade statistics on the front end
    - Using Redis to cache data every day
    - Have stats like: Most Traded, Least Wanted, Most Depost
- Have user accounts
    - Can then link games to user
    - Can have things like a shared box system across all the User's games

See the [open issues](https://github.com/Artrios/MA_Server/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTRIBUTING -->
## Contributing

You are more than welcome to contribute to this project either through making feature requests or PRs (though maybe ask beforehand if it's a large PR). I do ask that AI not be used in this project.

<p align="right">(<a href="#readme-top">back to top</a>)</p>


<!-- ACKNOWLEDGMENTS -->
## Acknowledgments

* The REON Team - 
* evandixon - For [their post](https://projectpokemon.org/home/docs/gen-5/gts-protocol-r19/) documenting the GTS Protocol on the DS games[]()


<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/Artrios/MA_Server.svg?style=for-the-badge
[contributors-url]: https://github.com/Artrios/MA_Server/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/Artrios/MA_Server.svg?style=for-the-badge
[forks-url]: https://github.com/Artrios/MA_Server/network/members
[stars-shield]: https://img.shields.io/github/stars/Artrios/MA_Server.svg?style=for-the-badge
[stars-url]: https://github.com/Artrios/MA_Server/stargazers
[issues-shield]: https://img.shields.io/github/issues/Artrios/MA_Server.svg?style=for-the-badge
[issues-url]: https://github.com/Artrios/MA_Server/issues
[license-shield]: https://img.shields.io/github/license/Artrios/MA_Server.svg?style=for-the-badge
[license-url]: https://github.com/Artrios/MA_Server/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[product-screenshot]: images/screenshot.png
