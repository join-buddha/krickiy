
;(function ($, window) {

    var post_id = window.fluxus_post_id || 0;

    $(function () {

        var $btnAdd = $('#fluxus-add-project-info'),
            $container = $('.fluxus-project-information tbody');

        $btnAdd.click(function (e) {

            e.preventDefault();

            var $title = $( 'input[name="fluxus_project_info_add_title"]' ),
                $content = $( 'textarea[name="fluxus_project_info_add_content"]' ),
                $newElement = $( '<tr class="fluxus-project" />' );

            $( '<input type="text" name="fluxus_project_info_title[]" />' ).val( $title.val() )
                                                                           .appendTo( $newElement )
                                                                           .wrap( '<td />' );

            $( '<textarea name="fluxus_project_info_content[]" />' ).val( $content.val() )
                                                                    .appendTo( $newElement )
                                                                    .wrap( '<td />' );

            $newElement.insertBefore( $container.find( '.add-element' ) );

            $title.val('');
            $content.val('');

        });

    });

    var ProjectMediaModel = Backbone.Model.extend({

        sync: function(method, model, options) {

            var params = {
                  type: method == 'read' ? 'GET' : 'POST',
                  dataType: 'json',
                  url: this.url(),
                  data: {}
                },
                dfd;

            options || (options = {});

            // Ensure that we have the appropriate request data.
            if (!options.data && model && (method == 'create' || method == 'update')) {
              params.data = model.toJSON();
            }

            params.data._method = method;
            params.data.test = '"';

            dfd = $.ajax(_.extend(params, options));
            Backbone.Events.trigger( 'syncing', method, model, dfd );

            return dfd;

        },

        url: function () {
            var portfolio_media_nonce = $('[name="portfolio_media_nonce"]').val();
            return ajaxurl + '?action=fluxus_project_media_save&post_id=' + post_id + ( this.id ? '&id=' + this.id : '' ) + '&nonce=' + portfolio_media_nonce;
        },

        defaults: function () {
            return {
                order:           0,
                type:            'image',
                embed:           '',
                filename:        '',
                description:     '',
                attachment_id:   false,
                thumbnail_url:   '',
                featured:        false,
                published:       true
            };
        },

        isImage: function () {
            return this.get('type') == 'image';
        },

        isVideo: function () {
            return this.get('type') == 'video';
        }

    });



    var ProjectMediaList = Backbone.Collection.extend({

        model: ProjectMediaModel,
        url: ajaxurl + '?action=fluxus_project_media&post_id=' + post_id

    });



    var ProjectMediaView = Backbone.View.extend({

        tagName: 'div',
        className: 'fluxus-item media-item',

        template: _.template($('#media-item-template').html()),

        events: {
          'click .btn-edit'           : 'editOpenEvent',
          'click .btn-cancel'         : 'editCloseEvent',
          'click .btn-delete'         : 'removeEvent',
          'click .btn-save'           : 'saveEvent',
          'click .btn-add-screenshot' : 'addScreenshotEvent'
        },

        mediaFrame: false,

        initialize: function () {

            this.model.on('destroy', this.remove, this);

        },

        render: function() {

            this.$el.html(this.template(this.model.toJSON()));
            this.$editPanel = this.$el.find('.item-edit');

            if (this.model.get('type') == 'video') {

                var $mediaPreview = this.$el.find('.media-preview');

                if ( this.model.get('embed').match(/\<iframe.+?src=".+?youtube\.com.+?"/) ) {
                    $mediaPreview.addClass('youtube-icon');
                } else if ( this.model.get('embed').match(/\<iframe.+?src=".+?vimeo\.com.+?"/) ) {
                    $mediaPreview.addClass('vimeo-icon');
                } else {
                    $mediaPreview.addClass('unknown-icon');
                }

            }

            this.$el.data('view', this);

            return this;
        },

        editOpenEvent: function (e) {
            e && e.preventDefault();
            this.$editPanel.is(':visible') ? this.closeEdit() : this.openEdit();
        },

        openEdit: function () {
            this.$editPanel.slideDown(300);
        },

        closeEdit: function () {
            this.$editPanel.slideUp(300);
        },

        editCloseEvent: function (e) {
            e && e.preventDefault();
            this.closeEdit();
        },

        removeEvent: function (e) {
            e && e.preventDefault();
            this.model.destroy();
        },

        saveEvent: function (e) {
            e && e.preventDefault();

            this.$el.addClass('fluxus-item-loading');

            var that = this,
                values = {
                    'description': this.model.isImage() ? this.$el.find('[name="description"]').val() : '',
                    'embed'      : this.model.isVideo() ? this.$el.find('[name="embed"]').val() : '',
                    'featured'   : this.$el.find('[name="featured"]').is(':checked') ? 1 : 0,
                    'published'  : this.$el.find('[name="published"]').is(':checked') ? 1 : 0
                };

            if (this.model.get('featured') != values.featured) {
                _.each(projectMediaApp.mediaViews, function (mediaView) {
                    if (mediaView != that) {
                        mediaView.model.set('featured', 0);
                        mediaView.render();
                    }
                });
            }

            this.model.save(values, {
                success: function () {
                    that.$el.removeClass('fluxus-item-loading');
                    that.render();
                },
                error: function () {
                    that.$el.removeClass('fluxus-item-loading');
                }
            });
            this.closeEdit();
        },

        addScreenshotEvent: function (e) {

            e && e.preventDefault();

            var that = this;

            if (this.mediaFrame) {

                this.mediaFrame.open();

            } else {

                this.mediaFrame = wp.media.frames.file_frame = wp.media({
                    title: $('#lang-select-screenshot').val(),
                    button: {
                        text: $('#lang-select-screenshot').val()
                    },
                    multiple: false
                });

                this.mediaFrame.on('select', function() {

                    var attachments = that.mediaFrame.state().get('selection').toJSON(),
                        attachment, thumbnail_url;

                    if ( attachments[0] ) {

                        attachment = attachments[0];

                        that.model.set('screenshot_id', attachment.id);

                        if (attachment.sizes && attachment.sizes.thumbnail) {
                            thumbnail_url = attachment.sizes.thumbnail.url;
                        } else if (attachment.sizes && attachment.sizes.full) {
                            thumbnail_url = attachment.sizes.full.url;
                        } else {
                            thumbnail_url = attachment.url;
                        }

                        that.$el.find('.media-preview').css('background-image', 'url(' + thumbnail_url + ')');

                    }

                });

                this.mediaFrame.open();

            }

        }

    });



    var ProjectMediaApp = Backbone.View.extend({

        el: $('#fluxus-project-media'),
        $media: $('#media-items-container'),

        events: {
            'click #fluxus-add-image':      'addImageEvent',
            'click #fluxus-add-video':      'addVideoEvent'
        },

        mediaViews: [],

        syncRequests: [],

        initialize: function() {

            var that = this;

            /**
             * Disables Publish buttons while backbone update/create/destroy requests are in progress.
             */
            Backbone.Events.on('syncing', function (method, model, dfd) {

              if ( method != 'read' ) {

                that.disablePublish();
                that.syncRequests.push(dfd);

                $.when(dfd).always(function () {
                  that.syncRequests.pop();
                  if (that.syncRequests.length == 0) {
                    that.enablePublish();
                  }
                });

              }

            });

            media.on('add', this.addOne, this);
            media.on('reset', this.addAll, this);

            media.fetch();

            /**
             * Enable JS sorting of slides.
             */
            this.$media.sortable({
                handle: '.fluxus-drag',
                items: '.media-item',
                axis: 'y',
                update: function () {

                    var $items = that.$media.children('.media-item'),
                        $itemsOrderInput = $('input[name=project_media_items_order]'),
                        serializedOrder = [];

                    $items.each(function () {
                        serializedOrder.push($(this).data('view').model.get('id'));
                    });

                    $itemsOrderInput.val(serializedOrder.join(','));

                }
            });

        },

        disablePublish: function () {
          $('#post-preview, #publish, #save').attr('disabled', 'disabled');
          $('#publishing-action .spinner').show();
        },

        enablePublish: function () {
          $('#post-preview, #publish, #save').removeAttr('disabled');
          $('#publishing-action .spinner').hide();
        },

        addOne: function(mediaItem) {
            var view = new ProjectMediaView({model: mediaItem});
            this.$media.append(view.render().el);
            this.mediaViews.push(view);
        },

        addAll: function() {
            this.mediaViews = [];
            media.each(this.addOne, this);
        },

        mediaFrame: false,

        addImageEvent: function (e) {

            var that = this,
                $t = $(e.currentTarget);

            if ($t.is('.button-disabled')) {
                return false;
            }

            e.preventDefault();

            // If the media frame already exists, reopen it.
            if ( this.mediaFrame ) {
              this.mediaFrame.open();
              return;
            }

            // Create the media frame.
            this.mediaFrame = wp.media.frames.file_frame = wp.media({
                title: this.$media.data('iframe-title'),
                button: {
                    text: this.$media.data('iframe-button')
                },
                multiple: true  // Multiple items allowed
            });

            this.mediaFrame.on('select', function() {

                var attachments = that.mediaFrame.state().get('selection').toJSON(),
                    items = [],
                    addSingleAttachment = function (items, itemIndex) {
                        var item = items[itemIndex];
                        item.save({}, {
                            success: function (model, response) {
                                var imageView = new ProjectMediaView({model: item});
                                response && response.id && (model.id = response.id);
                                that.$media.prepend(imageView.render().el);
                                if (typeof items[itemIndex - 1] !== 'undefined') {
                                    addSingleAttachment(items, itemIndex - 1);
                                }
                            }
                        });
                    };


                $.each(attachments, function () {

                    if (this.type != 'image') {
                        return;
                    }

                    if (this.sizes && this.sizes.thumbnail) {
                        thumbnail_url = this.sizes.thumbnail.url;
                    } else if (this.sizes && this.sizes.full) {
                        thumbnail_url = this.sizes.full.url;
                    } else {
                        thumbnail_url = this.url;
                    }

                    items.push(new ProjectMediaModel({
                                    type:           'image',
                                    attachment_id:  this.id,
                                    filename:       this.filename,
                                    thumbnail_url:  thumbnail_url
                                }));

                });

                // Adds attachment using sync requests
                items.length && addSingleAttachment(items, items.length - 1);

            });

            this.mediaFrame.open();

        },

        addVideoEvent: function (e) {

            e && e.preventDefault();

            var $t = $(e.currentTarget);

            if ($t.is('.button-disabled')) {
                return false;
            }

            var that = this,
                videoModel = new ProjectMediaModel({
                                    type:           'video',
                                    embed:          ''
                                }),
                videoView = new ProjectMediaView({model: videoModel});

            videoModel.save({}, {
                success: function (model, response) {
                    response && response.id && (model.id = response.id);
                    that.$media.prepend(videoView.render().el);
                    videoView.openEdit();
                }
            });

        }

    });


    // Our Media Collection
    if (post_id) {
        var media = new ProjectMediaList(),
            projectMediaApp = new ProjectMediaApp();
    }

})(jQuery, window);