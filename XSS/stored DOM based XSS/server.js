const express = require('express');
const fs = require('fs');
const app = express();
const PORT = 3000;
const DATA_FILE = 'data.json';

// Middleware to serve HTML/JS files from the current folder
app.use(express.static(__dirname));
// Middleware to parse incoming JSON data from POST requests
app.use(express.json()); 

// --- 1. POST /save-input: Stores new data ---
app.post('/save-input', (req, res) => {
    const newData = req.body;
    
    // Read existing data (or start with an empty array if the file is new/empty)
    let records = [];
    try {
        const fileContent = fs.readFileSync(DATA_FILE, 'utf8');
        records = JSON.parse(fileContent);
    } catch (e) {
        // Ignore file not found or JSON parse errors and proceed with an empty array
    }

    records.push(newData);
    
    // Write the updated array back to the file
    fs.writeFileSync(DATA_FILE, JSON.stringify(records, null, 2), 'utf8');
    
    res.json({ message: 'Data saved successfully!' });
});

// --- 2. GET /get-data: Retrieves all stored data ---
app.get('/get-data', (req, res) => {
    try {
        const data = fs.readFileSync(DATA_FILE, 'utf8');
        res.json(JSON.parse(data));
    } catch (e) {
        // Send an empty array if file reading or parsing fails
        res.json([]);
    }
});

app.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
});