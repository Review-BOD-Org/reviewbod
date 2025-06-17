<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Mermaid CDN Example</title>
  <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
</head>
<body>

  <!-- Your Mermaid diagram -->
  <div class="mermaid">
    graph TD;
      A-->B;
      A-->C;
      B-->D;
      C-->D;
  </div>

  <script>
    // Initialize Mermaid
    mermaid.initialize({ startOnLoad: true });
  </script>

</body>
</html>
