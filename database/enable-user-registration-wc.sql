-- Enable public user registration (Hostinger wc_ prefix)
UPDATE wc_options SET option_value = '1' WHERE option_name = 'users_can_register';
UPDATE wc_options SET option_value = 'subscriber' WHERE option_name = 'default_role';
-- Verify:
-- SELECT option_name, option_value FROM wc_options WHERE option_name IN ('users_can_register','default_role');
