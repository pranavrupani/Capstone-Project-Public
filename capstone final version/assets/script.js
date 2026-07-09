document.addEventListener('DOMContentLoaded', function(){
  // Find the mark-complete button on the page
  var markBtn = document.querySelector('.mark-complete');

  if (markBtn) {
    markBtn.addEventListener('click', function(e){
      e.preventDefault();

      // Find the count display element
      var countEl = document.getElementById('completed-count');
      if (!countEl) return;

      // Parse the current count value
      var current = parseInt(countEl.textContent || '0', 10);

      // Increment the count up to 9 lessons
      if (current < 9) current += 1;

      // Write the new count back to the page
      countEl.textContent = current;
    });
  }
});
