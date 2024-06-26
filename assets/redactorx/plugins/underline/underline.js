RedactorX.add("plugin", "underline", {
  defaults: {
    icon: '<svg height="16" viewbox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg"><path d="m12.2307692 12c.5522848 0 1 .4477153 1 1 0 .5128358-.3860402.9355072-.8833788.9932723l-.1166212.0067277h-8.2307692c-.55228475 0-1-.4477153-1-1 0-.5128358.38604019-.9355072.88337887-.9932723l.11662113-.0067277zm-1.2307692-10c.5128358 0 .9355072.43429521.9932723.99380123l.0067277.13119877v3.51108871c0 2.42071645-1.7998593 4.36391129-4 4.36391129-2.13138633 0-3.8871179-1.82364282-3.99476969-4.13841284l-.00523031-.22549845v-3.51108871c0-.62132034.44771525-1.125 1-1.125.51283584 0 .93550716.43429521.99327227.99380123l.00672773.13119877v3.51108871c0 1.15688618.88643222 2.11391129 2 2.11391129 1.06054074 0 1.91506284-.86804999 1.99404104-1.95008319l.00595896-.1638281v-3.51108871c0-.62132034.4477153-1.125 1-1.125z"/></svg>',
  },
  start: function () {
    var button = {
      title: "## buttons.underline ##",
      icon: this.opts.underline.icon,
      command: "inline.set",
      position: {
        after: "italic",
      },
      active: {
        tags: ["u"],
      },
      params: {
        tag: "u",
      },
      blocks: {
        all: "editable",
      },
    };

    this.app.toolbar.add("underline", button);
    this.app.context.add("underline", button);
  },
});
