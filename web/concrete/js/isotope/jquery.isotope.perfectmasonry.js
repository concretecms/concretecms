/*!
 * PerfectMasonry extension for Isotope
 *
 * Does similar things as the Isotopes "masonry" layoutmode, except that this one will actually go back and plug the holes
 * left by bigger elements, thus making a perfect brick wall. Profit!
 *
 *
 * @author Zonear Ltd. <contact@zonear.com>
 */
;(function($, undefined) {

  $.extend($.Isotope.prototype, {

    /**
     * Reset layout properties
     *
     * Runs before any layout change
     * -------------------------------------------------------------------------------------------------------- */
    _perfectMasonryReset: function() {

      // Setup layout properties
      var properties = this.perfectMasonry = {};

      // Get columnWidth & cols and rowHeight & rows (true argument) to properties
      this._getSegments();
          this._getSegments(true);

      // Calculate cols & rows
      this._perfectMasonryGetSegments();

      // Create top row of the grid
      properties.grid = new Array(this.perfectMasonry.cols);

      // Set container dimensions to 0
      properties.containerHeight = 0;
      properties.containerWidth = 0;
      },



      /**
     * Create layout
     * -------------------------------------------------------------------------------------------------------- */
    _perfectMasonryLayout: function($elems) {
      var instance = this,
        properties = this.perfectMasonry;

      // Loop each element
      $elems.each(function() {
        var $this  = $(this);

        // Element width & height
        var width = $this.outerWidth(true),
          height = $this.outerHeight(true),

          // How many columns/rows does the tile span
          colSpan = Math.ceil(width / properties.columnWidth),
          colSpan = Math.min(colSpan, properties.cols),
          rowSpan = Math.ceil(height / properties.rowHeight),
          rowSpan = Math.min(rowSpan, properties.cols);


        // Wider tiles can't fit into the last column
        var maxCol = properties.cols + 1 - colSpan;

        // Loop through rows
        var y = -1;
        while(true) {
          y++;

          // Add new rows as we go
          properties.grid[y] = properties.grid[y] || [];

          // Go through the cells in the row (columns)
          for (var x = 0; x < maxCol; x++) {

            // Does the tile fit here or not
            var doesFit = true;

            // If the tile is not free, move to the next one immediately
            var tile = properties.grid[y][x];
            if(tile) { continue; }


            // Tiles spanning to multiple rows/columns - Check if it'll fit
            if(colSpan > 1 || rowSpan > 1) {
              for (var i = 0; i < rowSpan; i++) {
                for (var j = 0; j < colSpan; j++) {

                  // If the row below is empty (undefined), we're alright
                  if(!properties.grid[y+i]) { continue; }

                  // Check if the cell is occupied - If yes, set doesFit to false and break
                  if(properties.grid[y+i][x+j]) { doesFit = false; break; }
                }

                // If it doesn't fit, don't waste our time here
                if(!doesFit) { break; }
              }
            }


            // If the shoe fits...
            if(doesFit) {

              // Fill the cells (handle elements that span multiple rows & columns)
              for (var i = 0; i < rowSpan; i++) {
                for (var j = 0; j < colSpan; j++) {

                  // Make sure the rows below current row are there
                  properties.grid[y+i] = properties.grid[y+i] || [];

                  // Set the item into the cell
                  properties.grid[y+i][x+j] = true;
                }
              }

              // Update container dimensions
              var newWidth = x * properties.columnWidth + width;
              if(newWidth > properties.containerWidth) { properties.containerWidth = newWidth; }
              var newHeight = y * properties.rowHeight + height;
              if(newHeight > properties.containerHeight) { properties.containerHeight = newHeight; }

              // Push element into the document and GTFO
              instance._pushPosition($this, x*properties.columnWidth, y*properties.rowHeight);
              return;
            }
          }
        }

        // If we got all the way down to here, the element can't fit - Hide it
        instance._pushPosition($this, -9999, -9999);
      });
    },



      /**
     * Get container size
     *
     * For resizing the container
     * -------------------------------------------------------------------------------------------------------- */
      _perfectMasonryGetContainerSize: function() {
      return {
        width: this.perfectMasonry.containerWidth,
        height: this.perfectMasonry.containerHeight
      };
      },



      /**
     * Resize changed
     *
     * Figure out if layout changed
     * -------------------------------------------------------------------------------------------------------- */
      _perfectMasonryResizeChanged: function() {
        var properties = this.perfectMasonry;

        // Store old col count and calculate new numbers
        var oldCols = properties.cols;
        this._perfectMasonryGetSegments();

      // If new count was different, force layout change
      if(oldCols !== properties.cols) { return true; }

        return false;
      },






      /**
     * Private
     * Do segment calculations by hand
     * -------------------------------------------------------------------------------------------------------- */
    _perfectMasonryGetSegments: function() {
      var properties = this.perfectMasonry;

      // Calculate columns
      var parentWidth = this.element.parent().width();
      properties.cols = Math.floor(parentWidth / properties.columnWidth) || 1;

      // Calculate rows
      var parentHeight = this.element.parent().height();
      properties.rows = Math.floor(parentHeight / properties.rowHeight) || 1;
    }
  });


})(jQuery);
