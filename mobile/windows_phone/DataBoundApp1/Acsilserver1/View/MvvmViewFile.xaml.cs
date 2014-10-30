using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Navigation;
using Microsoft.Phone.Controls;
using Microsoft.Phone.Shell;
using Acsilserver1.Resources;
using System.Windows.Data;
using System.Collections.ObjectModel;
using System.ComponentModel;
using System.Windows.Media.Imaging;

namespace Acsilserver1.View
{

    public partial class MvvmViewFile : PhoneApplicationPage
    {
        int index = -1;
        // Constructor
        public MvvmViewFile()
        {
            InitializeComponent();
        }


        protected override void OnNavigatedTo(System.Windows.Navigation.NavigationEventArgs e)
        {

            if (DataContext == null)
            {
                string selectedIndex = "";
                if (NavigationContext.QueryString.TryGetValue("selectedItem", out selectedIndex))
                {
                    index = int.Parse(selectedIndex);
                    DataContext = App.ViewModel.Items[index];
                    string type = App.ViewModel.Items[index].MimeType;
                    if ((type == "avi") || (type == "mp4"))
                    {
                        MediaElement.Visibility = Visibility.Visible;
                        MediaElementExtender.Visibility = Visibility.Visible;
                        Position.Visibility = Visibility.Visible;
                        TotalTime.Visibility = Visibility.Visible;
                        Progress.Visibility = Visibility.Visible;

                        MediaElement.Height = MediaElement.Height;
                        MediaElement.Width = MediaElement.Width;

                    }
                    else if (type == "mpga")
                    {

                        MediaElement.Visibility = Visibility.Visible;
                        MediaElementExtender.Visibility = Visibility.Visible;
                        Position.Visibility = Visibility.Visible;
                        TotalTime.Visibility = Visibility.Visible;
                        Progress.Visibility = Visibility.Visible;
                        
 
                        BitmapImage bi3 = new BitmapImage();

                        bi3.UriSource = new Uri("/Assets/Icon/music.png", UriKind.Relative);

                        FileImage.Source = bi3;
                        FileImage.Visibility = Visibility.Visible;

                        MediaElement.Width = FileImage.Width;
                        MediaElement.Height = MediaElement.Height;
                    }
                    else if ((type == "jpeg") || (type == "png"))
                    {
                        BitmapImage bi3 = new BitmapImage();

                        bi3.UriSource = new Uri(App.ViewModel.Items[index].URL);
                        FileImage.Source = bi3;
                        FileImage.Visibility = Visibility.Visible;
                    }
                    else
                    {
                        BitmapImage bi3 = new BitmapImage();

                        bi3.UriSource = new Uri("/Assets/Icon/nonVisible.png", UriKind.Relative);

                        FileImage.Source = bi3;
                        FileImage.Visibility = Visibility.Visible;
                        
                    }
                }
            }


            MediaElement.Play();

            base.OnNavigatedTo(e);
        }

        private void Media_Tap(object sender, System.Windows.Input.GestureEventArgs e)
        {
            if (MediaElement.CurrentState == System.Windows.Media.MediaElementState.Playing)
            {
                MediaElement.Pause();
                //Status.Text = "Play";
            }
            else
            {
                MediaElement.Play();
                //Status.Text = "Pause";
            }
        }

        protected override void OnBackKeyPress(System.ComponentModel.CancelEventArgs e)
        {
            MediaElement.Stop();

            base.OnBackKeyPress(e);
        }

        private void AppBarDelete_Click(object sender, EventArgs e)
        {

            if (MessageBox.Show("Voulez vous supprimer ce fichier?", "Attention", MessageBoxButton.OKCancel) == MessageBoxResult.Cancel)
            {

            }
            else
            {
                App.ViewModel.Items.RemoveAt(index);
                NavigationService.GoBack();
            }

        }

        private void AppBarDetails_Click(object sender, EventArgs e)
        {
            NavigationService.Navigate(new Uri("/View/MvvmViewDetails.xaml?selectedItem=" + index, UriKind.Relative));
        }

        private void Status_Click(object sender, EventArgs e)
        {
            if (MediaElement.CurrentState == System.Windows.Media.MediaElementState.Playing)
            {
                MediaElement.Pause();
               
            }
            else
            {
                MediaElement.Play();
              
            }
        }


    }
}