/**
 * This a minimal example for running AlgebraKiT exercises. This file 
 * contains the backend part (creating a session from an exercise-id).
 * The frontend part is in de views/index.ejs file (inserting the akit-exercise tags).
 * 
 * To run this code, please do the following:
 * - Make sure that you have an AlgebraKiT API key and have stored this key in the AKIT_API_KEY environment var
 * - Make sure you are running the latest version of node
 * - run npm install
 * - run npm start
 * - Browse to http://localhost:3300/demo. Three AlgebraKiT exercise sessions should be created for you and will appear in your browser.
 */
const express = require('express');
const request = require('request');

const apiKey = process.env.AKIT_API_KEY; //The API key that you created in the management console
const host = 'https://algebrakit.eu';   //the domain of AlgebraKiT's web service
const endpoint = '/session/create';         //see https://algebrakit-learning.com/dev/api-web-create

const data = {
    'exercises': [
        {
            'exerciseId': "9e5aa8cd-1426-4845-88d6-459d3942ca75",   //exercise id can be obtained from AlgebraKiT's CMS
            'version': "latest",
        },
        {
            'exerciseId': "d098c2cc-b100-4e99-91cb-ca65af683abe",
            'version': "latest",
        },
        {
            'exerciseId': "be98a21c-5f1f-45c0-919f-e511ac55fc08",
            'version': "latest",
        },
    ],
    'api-version': 2
};

const app = express();
const port = 3300
app.set('view engine', 'ejs');

app.get('/demo', (req, res) => {

    let options = {
        url: host+endpoint,
        method: "POST",
        body: data,
        headers: {
            'x-api-key': apiKey
        },
        json: true
    }

    //Use the AlgebraKiT API to create a session
    request(options, (error, response, body) => {
        if (error) {
            res.status(500).end(error);
            return;
        }
        //Render the results
        res.render('index', {
            exResults: body,
        });
    })
})

app.listen(port, () => console.log(`AlgebraKiT demo app listening on port ${port}!`))