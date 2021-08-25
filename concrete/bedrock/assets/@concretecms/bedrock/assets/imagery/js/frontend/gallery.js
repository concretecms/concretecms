$('[data-gallery-lightbox=true]').magnificPopup({
    type: 'image',
    gallery: {
        enabled: true
    },
    image: {
        titleSrc: function (item) {
            var $wrapper = $('<div />')
            var caption = item.el.attr('data-caption')
            $wrapper.append(caption)

            var downloadLink = item.el.attr('data-download-link');
            if (downloadLink.length) {
                var $a = $("<a></a>");
                $a.attr("href", downloadLink);
                $a.attr("target", "_blank");
                $a.attr("class", "ms-2");
                $a.html("Download");
                $wrapper.append($a)
            }

            return $wrapper.html()

        }
    }
});