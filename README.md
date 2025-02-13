# Symfony Docker Boilerplate

This project is a boilerplate for setting up a Symfony application using Docker and Makefile. It includes services for PHP-FPM and Nginx, providing an isolated development environment.

## 🚀 Features

- Symfony 7.0 skeleton installation.
- PHP-FPM container for executing PHP.
- Nginx container for serving the application.
- Easy-to-use `Makefile` for common tasks (building, starting, stopping, and clearing cache).
- Docker Compose configuration for containerized setup.
- PHPCS: Code style enforcement.
- PHPStan: Static code analysis for finding bugs.
- PHPUnit: Unit testing framework.

---

## 🛠️ Requirements

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Make](https://www.gnu.org/software/make/)

---

## 📦 Installation

1. Clone this repository:
```bash
  git clone <repository-url>
  cd <repository-directory>
```
   
2. Initialize the Symfony project:

```bash
  make init
```
The application will be available at: http://127.0.0.1:8080/. <br>
Your Symfony application will be available under the app folder.


## 🔧 Available Commands

### App
| Command        | Description                      |
|:---------------|:---------------------------------|
| `make init`    | Initialize a new Symfony project |
| `make cc`      | Clear the Symfony cache          |


### Composer
| Command                        | Description                        |
|:-------------------------------|:-----------------------------------|
| `make composer-install`        | Install composer dependencies      |
| `make composer-update`         | Update composer dependencies       |
| `make composer-clear-cache`    | Clear composer cache               |

### Docker
| Command                       | Description                       |
|:------------------------------|:----------------------------------|
| `make build`                  | Build Docker images               |
| `make rebuild`                | Rebuild services and volumes      |
| `make start`                  | Start Docker containers           |
| `make stop`                   | Stop Docker containers            |
| `make prune`                  | Remove unused Docker volumes      |
| `make logs`                   | Display container logs            |
| `make exec-php-fpm`           | Open bash in PHP-FPM container    |
| `make exec-nginx`             | Open shell in Nginx container     |

### Quality Tools
| Command               | Description                             |
|:----------------------|:----------------------------------------|
| `make phpcs`          | Check code style based on PSR-12        |
| `make phpcbf`         | Fix coding style issues based on PSR-12 |
| `make phpstan`        | Analyze code for bugs and errors        |
| `make phpunit`        | Run unit tests                          |

### Helpers
| Command      | Description                  |
|:-------------|:-----------------------------|
| `make help`  | List all available commands  |

# 🐳 Docker Services
## PHP-FPM
- Built from the ./docker/php-fpm context.
- Customizable through USER_UID, USER_GID, and TIMEZONE build arguments.
- Mounts:
  - ./app to /var/www (Symfony application).
  - ./docker/php-fpm/logs/ to /var/log.

## Nginx
- Based on nginx:alpine.
- Serves the application on http://127.0.0.1:8080/.
- Mounts:
  - ./app to /var/www (Symfony application).
  - /docker/nginx/nginx.conf to /etc/nginx/conf.d/default.conf.
  - /docker/nginx/logs/ to /var/log/nginx.

# 🛠️ Customization
  - Modify ./docker/nginx/nginx.conf to customize the Nginx configuration.
  - Adjust ./docker/php-fpm/Dockerfile for PHP-FPM customizations.
  - Update .env to configure variables like USER_UID, USER_GID, and TIMEZONE.

> To customize the Symfony version, you must modify the base PHP-FPM image with the right version and adjust the Makefile to use the appropriate Symfony skeleton for project creation.

# 📚 Useful Links
  - [Symfony Documentation](https://symfony.com/doc/7.0/index.html)
 -  [Composer](https://getcomposer.org/doc/)

#  📝 License
  This project is licensed under the MIT License.


## 💡 Feedback and Contributions

I welcome feedback, suggestions, and contributions to help improve this project! If you have an idea for a new feature, found a bug, or have any other feedback, feel free to create an issue.

### How to Get Involved:
1. **Propose Enhancements:**  
   Have an idea to improve the project? Open a new issue and describe your suggestion in detail.  
   👉 [Create an enhancement issue](https://github.com/mb2dev/Symfony-Docker-Boilerplate/issues/new?labels=enhancement&template=feature_request.md)

2. **Report Bugs:**  
   Encountered a bug? Let us know by creating a bug report issue.  
   👉 [Create a bug report issue](https://github.com/mb2dev/Symfony-Docker-Boilerplate/issues/new?labels=bug&template=bug_report.md)

3. **Ask Questions or Request Features:**  
   If you're unsure about something or want a specific feature, open a general issue.  
   👉 [Create a general issue](https://github.com/mb2dev/Symfony-Docker-Boilerplate/issues/new)

### Guidelines:
- Provide as much detail as possible to help us understand your request.
- Be respectful and constructive in your communication.
