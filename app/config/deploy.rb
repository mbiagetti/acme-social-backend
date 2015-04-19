set :stages,        %w(prod)
set :default_stage, "prod"
set :stage_dir,     "app/config/deploy"
require 'capistrano/ext/multistage'


set :application, "Spike"
set :app_path,    "app"

set :repository,  "https://github.com/mbiagetti/acme-social-backend.git"
set :scm,         :git

set :deploy_via, :remote_cache
set :use_sudo,      false
set :keep_releases, 3

after "deploy:update", "deploy:cleanup"

### Symfony Section ###
set :shared_files,      ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", web_path + "/images", "vendor"]
set :use_composer, true
set :update_vendors, false
set :copy_vendors, true

