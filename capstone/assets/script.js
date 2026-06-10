document.addEventListener('DOMContentLoaded', function(){
  // Get the mark-complete button element
  var markBtn = document.querySelector('.mark-complete');
  
  // Only proceed if the button exists (it's only on the course page)
  if(markBtn){
    // Attach click event listener to the button
    markBtn.addEventListener('click', function(e){
      // Prevent default button behavior
      e.preventDefault();
      
      // Get the element that displays the count of completed courses
      var countEl = document.getElementById('completed-count');
      
      // Safety check - if element doesn't exist, exit function
      if(!countEl) return;
      
      // Get the current count value and convert to integer
      // Default to 0 if the element is empty
      var current = parseInt(countEl.textContent || '0', 10);
      
      // Increment the count, but don't exceed 3 (total number of courses)
      if(current < 3) current += 1;
      
      // Update the display with the new count
      countEl.textContent = current;
        });
  }
});
