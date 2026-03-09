-- Add postedAt column to jobs table
ALTER TABLE jobs 
ADD COLUMN postedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Update existing jobs to have postedAt = createdAt for existing records
UPDATE jobs SET postedAt = createdAt WHERE postedAt IS NULL;
