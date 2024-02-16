import { createClient } from '@supabase/supabase-js'

export const supabase = createClient('https://nnbuvyjitnopekbbmzme.supabase.co', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5uYnV2eWppdG5vcGVrYmJtem1lIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MDgwNzEwMzIsImV4cCI6MjAyMzY0NzAzMn0.jjwC_4FURg2xwkj3-jmsDBINVutFSxNOnHUIvfHa8dE')