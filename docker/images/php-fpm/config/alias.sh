alias ll='ls -alF'

# symfony aliases
alias sf="php bin/console"
alias phpunit="source .env && php vendor/bin/phpunit $1"
alias db:drop:test="sf doctrine:database:drop --force --if-exists --env=test"
alias db:create:test="sf doctrine:database:create --if-not-exists --env=test"
alias db:migrate:test="sf doctrine:migrations:migrate --no-interaction --env=test"
