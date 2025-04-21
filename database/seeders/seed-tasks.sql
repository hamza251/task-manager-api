INSERT INTO tasks (title, description, completed) VALUES
('Sample Task 1', 'Sample task 1 description', FALSE),
('Sample Task 2', 'Sample task 2 description.', FALSE)
ON CONFLICT (id) DO NOTHING;