<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Stock Report</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    </head>
    <body class="container w-25">
        <div class="row">
            <div id="errors" class="alert alert-danger" style="display: none;">
            </div>
            <form id="form" class="col">
                <div class="mb-3">
                    <label class="form-label">Upload Excel File</label>
                    <input type="file" name="input_csv" id="input_csv" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Select Stock</label>
                    <select class="form-select form-select-md" name="input_stock" id="input_stock">
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="input_startdate" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="input_enddate" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <div id="results" class="alert alert-primary" style="display: none;">
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
        <script>
            function csvToArray(str, delimiter = ",") {
                // slice from start of text to the first \n index
                // use split to create an array from string by delimiter
                const headers = str.slice(0, str.indexOf("\n")).split(delimiter);

                // slice from \n index + 1 to the end of the text
                // use split to create an array of each csv value row
                const rows = str.slice(str.indexOf("\n") + 1).split("\n");

                // Map the rows
                // split values from each row into an array
                // use headers.reduce to create an object
                // object properties derived from headers:values
                // the object passed as an element of the array
                const arr = rows.map(function (row) {
                    const values = row.split(delimiter);
                    const el = headers.reduce(function (object, header, index) {
                    object[header] = values[index];
                    return object;
                    }, {});
                    return el;
                });

                // return the array
                return arr;
            };

            function populateStockDropdown(data) {
                const stockSet = new Set();
                for (let d of data) {
                    if (d.stock_name) {
                        const stock = d.stock_name.trim();
                        if (stock) {
                            stockSet.add(stock.toUpperCase());
                        }
                    }
                }

                let htmlDropdown = "<option selected disabled>-- Please Select --</option>";

                for (let stock of stockSet) {
                    htmlDropdown += "<option value='" + stock + "'>" + stock + "</option>"
                }
                document.getElementById("input_stock").innerHTML = htmlDropdown;
            };

            function getStockOutput() {

                fetch("stock_analyzer.php", {
                    method: 'POST',
                    body: new FormData(document.getElementById("form")),
                    headers: {
                        'Accept': 'application/json'
                    },
                }).then(response => response.json())
                .then(data => {
                    const errDOM = document.getElementById("errors");
                    const resultDOM = document.getElementById("results");
                    if (data.success) {

                        errDOM.style.display = "none";

                        let resultHtml = ""
                        resultHtml += "<p> Buy Date: " + data.data.buydate + "</p>";
                        resultHtml += "<p> Sell Date: " + data.data.selldate + "</p>";
                        resultHtml += "<p> Profit: " + data.data.profit + "</p>";
                        resultHtml += "<p> Mean Price: " + data.data.mean + "</p>";
                        resultHtml += "<p> Standard Deviation: " + data.data.standardDeviation + "</p>";

                        resultDOM.innerHTML = resultHtml;
                        resultDOM.style.display = "block"

                    } else {

                        resultDOM.style.display = "none";

                        let errorHtml = ""
                        for (let error of data.errors) {
                            errorHtml += "<p>" + error + "</p>";
                        }
                        errDOM.innerHTML = errorHtml;
                        errDOM.style.display = "block"
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
            }

            window.onload = function () {
                document.getElementById("input_csv").onchange = function (e) {
                    if (e.target.files.length) {
                        const inputFile = e.target.files[0];
                        const reader = new FileReader();

                        reader.onload = function (e) {
                            const text = e.target.result;
                            const data = csvToArray(text);
                            populateStockDropdown(data);
                        };
                        reader.readAsText(inputFile);
                    }
                }

                document.getElementById("form").onsubmit = function (e) {
                    e.preventDefault();
                    getStockOutput();
                }
            }
        </script>
    </body>
</html>