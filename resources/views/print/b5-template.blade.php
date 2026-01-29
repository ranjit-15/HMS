<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>B5 Print Template</title>
  <link rel="stylesheet" href="{{ asset('css/print-b5.css') }}">
  <style>body{background:#f3f4f6;padding:18px}</style>
</head>
<body>
  <div class="b5-page" role="document" aria-label="B5 Page">
    <div class="page-content">
      <h1 class="title">Project Title: Your Project Name</h1>

      <h2 class="section-heading">Abstract</h2>
      <div class="col-span-12">
        <p class="p">Short abstract text... (11.5pt, line-height 1.5).</p>
      </div>

      <h2 class="section-heading">Introduction</h2>
      <div class="col-span-8">
        <p class="p">Main body content, spans 8/12 columns for a readable text column.</p>
      </div>

      <div class="col-span-4">
        <div class="figure">
          <img src="{{ asset('images/techspire-logo.png') }}" alt="Diagram" style="width:100%;height:auto;display:block" />
          <div class="caption">Figure 1. Example diagram caption (9.5pt)</div>
        </div>
      </div>

      <div class="col-span-12">
        <pre class="code">/* Code snippet — monospace, readable */
function example() {
  console.log('hello B5');
}</pre>
      </div>

      <div class="col-span-12 figure">
        <table class="table">
          <thead>
            <tr><th>Metric</th><th>Value</th></tr>
          </thead>
          <tbody>
            <tr><td>Rows</td><td>42</td></tr>
          </tbody>
        </table>
        <div class="caption">Table 1. Experimental results</div>
      </div>

    </div>

    <div class="page-footer">Techspire College • Project — Page <span class="page-number">1</span></div>
  </div>
</body>
</html>
