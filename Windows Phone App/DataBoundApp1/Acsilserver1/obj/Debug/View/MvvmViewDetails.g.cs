﻿#pragma checksum "C:\Users\Edouard\Documents\GitHub\acsilserver\Windows Phone App\DataBoundApp1\Acsilserver1\View\MvvmViewDetails.xaml" "{406ea660-64cf-4c82-b6f0-42d48172a799}" "2ACB992F8544FE32BE05142DB5872880"
//------------------------------------------------------------------------------
// <auto-generated>
//     Ce code a été généré par un outil.
//     Version du runtime :4.0.30319.34014
//
//     Les modifications apportées à ce fichier peuvent provoquer un comportement incorrect et seront perdues si
//     le code est régénéré.
// </auto-generated>
//------------------------------------------------------------------------------

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
    
    
    public partial class MvvmViewDetails : Microsoft.Phone.Controls.PhoneApplicationPage {
        
        internal System.Windows.Controls.Grid LayoutRoot;
        
        internal System.Windows.Controls.StackPanel TitlePanel;
        
        internal System.Windows.Controls.TextBlock FileName;
        
        internal System.Windows.Controls.Grid ContentPanel;
        
        internal System.Windows.Controls.TextBox TextBoxName;
        
        internal Microsoft.Phone.Shell.ApplicationBarIconButton AppBarEdit;
        
        internal Microsoft.Phone.Shell.ApplicationBarIconButton AppBarSave;
        
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
            System.Windows.Application.LoadComponent(this, new System.Uri("/Acsilserver1;component/View/MvvmViewDetails.xaml", System.UriKind.Relative));
            this.LayoutRoot = ((System.Windows.Controls.Grid)(this.FindName("LayoutRoot")));
            this.TitlePanel = ((System.Windows.Controls.StackPanel)(this.FindName("TitlePanel")));
            this.FileName = ((System.Windows.Controls.TextBlock)(this.FindName("FileName")));
            this.ContentPanel = ((System.Windows.Controls.Grid)(this.FindName("ContentPanel")));
            this.TextBoxName = ((System.Windows.Controls.TextBox)(this.FindName("TextBoxName")));
            this.AppBarEdit = ((Microsoft.Phone.Shell.ApplicationBarIconButton)(this.FindName("AppBarEdit")));
            this.AppBarSave = ((Microsoft.Phone.Shell.ApplicationBarIconButton)(this.FindName("AppBarSave")));
        }
    }
}

