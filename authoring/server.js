const express = require('express');
const request = require('request');
const fs = require('fs');

const { createProxyMiddleware } = require('http-proxy-middleware');

const app = express();
const port = Number(process.env.PORT || 3015);

const API_KEY = '...'; //The API key that you received from AlgebraKiT or created in the management console

const TARGET = 'https://algebrakit.eu';
const WIDGET_TARGET = 'https://widgets.algebrakit.eu';

//setup proxy for AlgebraKiT gateway
var proxySecure = createProxyMiddleware({
    target: TARGET,
    changeOrigin: true,
    pathRewrite: {
        '^/algebrakit-secure' : '/'  // remove base path 
    },
    onProxyReq: function (proxyReq, req, res) {
        proxyReq.setHeader('x-api-key', API_KEY);
    }
});

var proxySessions = createProxyMiddleware({
    target: TARGET,
    changeOrigin: true,
    pathRewrite: {
        '^/algebrakit' : '/'  // remove base path 
    }
});

var proxyWidget = createProxyMiddleware({
    target: WIDGET_TARGET,
    changeOrigin: true,
    pathRewrite: {
        '^/widget' : '/'  // remove base path 
    }
});

app.use('/algebrakit-secure', proxySecure);
app.use('/algebrakit', proxySessions);
app.use('/widget', proxyWidget);

app.use('/restore*', function(req, res) {
    res.status(200).sendFile('/restore.html', {root: __dirname});
});
app.use('/review*', function(req, res) {
    res.status(200).sendFile('/review.html', {root: __dirname});
});
app.use('/solution*', function(req, res) {
    res.status(200).sendFile('/solution.html', {root: __dirname});
});

app.use('/assessment/:id', function(req, res) {

    let id = req.params['id'];
    jsonRequest(TARGET+'/session/create', 'POST', {
        "exercises": [{
            "exerciseId": id,
            "version": 'latest'
        }],
        // "scoringModel": "infinitas",
        "assessmentMode": true,
        "api-version": 2
    }).then(resp => {
        let html = fs.readFileSync(__dirname+'/assessment.html', {encoding:'utf8'});
        html = html.replace(/%SESSIONID%/g, resp[0].sessions[0].sessionId);
        res.status(200).end(html);
    });
})
app.use('/*', function(req, res) {
    res.status(200).sendFile('/exercise-editor.html', {root: __dirname});
})

app.listen(port, function() {
  console.log(`Listening at ${port}`);
});



function jsonRequest(url, method, data) {
    return new Promise(function (resolve, reject) {

        let body = {
            json: data,
            headers: {
                'x-api-key': API_KEY
            }
        }

        let callBack = function (error, response, body) {
            try {
                if (response == undefined) response = {};
                if (!error && response.statusCode === 200 && !(body && body.errorMessage)) {
                    resolve(body);
                } else {
                    console.log('Call to '+url+' failed');
                }
            } catch (err) { reject(err); }
        }

        switch (method) {
            case 'GET':
                request.get(url, callBack);
                break;
            case 'PUT':
                request.put(url, body, callBack);
                break;
            case 'DELETE':
                request.delete(url, body, callBack);
                break;
            default:
                request.post(url, body, callBack); //POST is default
                break;
        }
    });
}
