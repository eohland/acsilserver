﻿#pragma checksum "C:\Users\Edouard\Documents\ACSIL\DataBoundApp1\Acsilserver1\View\MvvmViewFile.xaml" "{406ea660-64cf-4c82-b6f0-42d48172a799}" "E1A2B0D5F2ACB2E40DB33A6DB87ABE23"
//------------------------------------------------------------------------------
// <auto-generated>
//     Ce code a été généré par un outil.
//     Version du runtime :4.0.30319.34014
//
//     Les modifications apportées à ce fichier peuvent provoquer un comportement incorrect et seront perdues si
//     le code est régénéré.
// </auto-generated>
//------------------------------------------------------------------------------

using Acsilserver1.View;
using Microsoft.Phone.Controls;
using Microsoft.Phone.Shell;
using System;
using System.Windows;
using System.Windows.Automation;
using System.Windows.Automation.Peers;
using System.Windows.Automation.Provider;
using System.Windows.Controls;
using System.Windows.Controls.Primitives;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Ink;
using System.Windows.Input;
using System.Windows.Interop;
using System.Windows.Markup;
using System.Windows.Media;
using System.Windows.Media.Animation;
using System.Windows.Media.Imaging;
using System.Windows.Resources;
using System.Windows.Shapes;
using System.Windows.Threading;


namespace Acsilserver1.View {
    
    
    public partial class MvvmViewFile : Microsoft.Phone.Controls.PhoneApplicationPage {
        
        internal System.Windows.Controls.Grid LayoutRoot;
        
        internal System.Windows.Controls.StackPanel TitlePanel;
        
        internal System.Windows.Controls.TextBlock FileName;
        
        internal System.Windows.Controls.Grid ContentPanel;
        
        internal System.Windows.Controls.Image FileImage;
        
        internal System.Windows.Controls.MediaElement MediaElement;
        
        internal Acsilserver1.View.MediaElementExtender MediaElementExtender;
        
        internal System.Windows.Controls.TextBlock Position;
        
        internal System.Windows.Controls.TextBlock TotalTime;
        
        internal System.Windows.Controls.ProgressBar Progress;
        
        internal Microsoft.Phone.Shell.ApplicationBarIconButton AppBarDetails;
        
        private bool _contentLoaded;
        
        /// <summary>
        /// InitializeComponent
        /// </summary>
        [System.Diagnostics.DebuggerNonUserCodeAttribute()]
        public void InitializeComponent() {
            if (_contentLoaded) {
                return;
            }
            _contentLoaded = true;
            System.Windows.Application.LoadComponent(this, new System.Uri("/Acsilserver1;component/View/MvvmViewFile.xaml", System.UriKind.Relative));
            this.LayoutRoot = ((System.Windows.Controls.Grid)(this.FindName("LayoutRoot")));
            this.TitlePanel = ((System.Windows.Controls.StackPanel)(this.FindName("TitlePanel")));
            this.FileName = ((System.Windows.Controls.TextBlock)(this.FindName("FileName")));
            this.ContentPanel = ((System.Windows.Controls.Grid)(this.FindName("ContentPanel")));
            this.FileImage = ((System.Windows.Controls.Image)(this.FindName("FileImage")));
            this.MediaElement = ((System.Windows.Controls.MediaElement)(this.FindName("MediaElement")));
            this.MediaElementExtender = ((Acsilserver1.View.MediaElementExtender)(this.FindName("MediaElementExtender")));
            this.Position = ((System.Windows.Controls.TextBlock)(this.FindName("Position")));
            this.TotalTime = ((System.Windows.Controls.TextBlock)(this.FindName("TotalTime")));
            this.Progress = ((System.Windows.Controls.ProgressBar)(this.FindName("Progress")));
            this.AppBarDetails = ((Microsoft.Phone.Shell.ApplicationBarIconButton)(this.FindName("AppBarDetails")));
        }
    }
}

