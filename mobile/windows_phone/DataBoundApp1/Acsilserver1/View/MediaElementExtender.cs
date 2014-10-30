using System;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Documents;
using System.Windows.Ink;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Animation;
using System.Windows.Shapes;
using System.Windows.Data;


public delegate void MarkerRoutedEventHandler(object sender, TimelineMarker marker);

namespace Acsilserver1.View
{



    public class MediaElementExtender : Control
    {
        /// <summary>
        /// MediaElement
        /// </summary>

        public MediaElement MediaElement
        {
            get { return (MediaElement)GetValue(MediaElementProperty); }
            set { SetValue(MediaElementProperty, value); }
        }

        // Using a DependencyProperty as the backing store for MediaElement.  This enables animation, styling, binding, etc...
        public static readonly DependencyProperty MediaElementProperty =
            DependencyProperty.Register("MediaElement", typeof(MediaElement), typeof(MediaElementExtender), new PropertyMetadata(null, OnMediaElementChanged));

        /// <summary>
        /// Le MediaElement a changé
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>

        private static void OnMediaElementChanged(DependencyObject sender, DependencyPropertyChangedEventArgs e)
        {
            MediaElementExtender mediaElementExtender = sender as MediaElementExtender;
            MediaElement newMediaElement = e.NewValue as MediaElement;
            MediaElement oldMediaElement = e.OldValue as MediaElement;

            // on revient à un contexte propre au controle
            mediaElementExtender.MediaElementChange(newMediaElement, oldMediaElement);
        }

        /// <summary>
        /// Changement du MediaElement
        /// </summary>
        /// <param name="newMediaElement"></param>
        /// <param name="oldMediaElement"></param>

        private void MediaElementChange(MediaElement newMediaElement, MediaElement oldMediaElement)
        {
            if (newMediaElement != null)
            {
                newMediaElement.MediaOpened += new RoutedEventHandler(MediaOpened);
            }

            if (oldMediaElement != null)
            {
                oldMediaElement.MediaOpened -= new RoutedEventHandler(MediaOpened);
            }
        }

        /// <summary>
        /// le media est pret a être lu
        void MediaOpened(object sender, RoutedEventArgs e)
        {
            MediaElement mediaElement = sender as MediaElement;
            
            // on copie la valeur
            this.NaturalDuration = mediaElement.NaturalDuration;
        }

        /// <summary>
        /// NaturalDuration
        /// </summary>

        public Duration NaturalDuration
        {
            get { return (Duration)GetValue(NaturalDurationProperty); }
            set { SetValue(NaturalDurationProperty, value); }
        }

        // Using a DependencyProperty as the backing store for NaturalDuration.  This enables animation, styling, binding, etc...
        public static readonly DependencyProperty NaturalDurationProperty =
            DependencyProperty.Register("NaturalDuration", typeof(Duration), typeof(MediaElementExtender), null);
    }
}
    

