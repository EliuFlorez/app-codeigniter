/*!
* Start Bootstrap - Blog Home v5.0.9 (https://startbootstrap.com/template/blog-home)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-blog-home/blob/master/LICENSE)
*/
// This file is intentionally blank
// Use this file to add JavaScript to your project

function blogList(search) {
    var query = (search) ? "?search="+search : "";
    axios.get('/blogs'+query)
        .then(function (response) {
            console.log(response);
            var blogs = response.data.data;
            $('#blog-list').empty();
            $.each(blogs, function(index, blog) {
                var truncatedContent = blog.content.substring(0, 70);
                var $card = $('<div class="card mb-4">');
                var $cardBody = $('<div class="card-body">');
                var $date = $('<div class="small text-muted">').text(blog.created_at);
                var $title = $('<h2 class="card-title">').text(blog.title);
                var $content = $('<p class="card-text">').text(truncatedContent+"...");
                var $readMore = $('<a class="btn btn-primary" href="#!">').text('Read more →').click(function() {
                    $content.text(blog.content);
                    $readMore.remove();
                });

                $cardBody.append($date, $title, $content, $readMore);
                $card.append($cardBody);

                $('#blog-list').append($card);
            });

            var authors = response.data.authors;
            $('#author-list').empty();
            $.each(authors, function(index, author) {
                var $li = $('<li>').append($('<a>', {
                    href: 'javascript:void(0)',
                    text: author.author,
                    class: 'author-link',
                    'data-author': author.author
                }));
                $('#author-list').append($li);
            });
        })
        .catch(function (error) {
            console.error(error);
        });
}

function blogClean() {
    $('#title').val("");
    $('#author').val("");
    $('#content').val("");
    $('#newModal').modal('hide');
}
$(document).on('click', '.author-link', function(e) {
    e.preventDefault();
    var authorName = $(this).data('author');
    console.log("authorName: ", authorName);
    blogList(authorName);
});
$(function() {
    $('#searchForm').submit(function(event) {
        blogList($('#search-term').val());
    });
    $('#createBlogForm').submit(function(e) {
        e.preventDefault();
        var formData = {
            'title': $('#title').val(),
            'author': $('#author').val(),
            'content': $('#content').val()
        };
        axios.post('/blogs', formData)
            .then(function (response) {
                console.log(response);
                blogClean();
                blogList();
            })
            .catch(function (error) {
                console.error(error);
            });
    });

    blogList("");
});