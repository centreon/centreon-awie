import groovy.json.JsonSlurper

/*
** Variables.
*/
properties([buildDiscarder(logRotator(numToKeepStr: '50'))])
def serie = '20.10'
def maintenanceBranch = "${serie}.x"
env.PROJECT='centreon-awie'

def checkoutCentreonBuild() {
  dir('centreon-build') {
    checkout resolveScm(source: [$class: 'GitSCMSource',
      remote: 'https://github.com/centreon/centreon-build.git',
      credentialsId: 'technique-ci',
      traits: [[$class: 'jenkins.plugins.git.traits.BranchDiscoveryTrait']]],
      targets: [env.BRANCH_NAME, 'master'])
  }
}

if (env.BRANCH_NAME.startsWith('release-')) {
  env.BUILD = 'RELEASE'
} else if ((env.BRANCH_NAME == 'master') || (env.BRANCH_NAME == maintenanceBranch)) {
  env.BUILD = 'REFERENCE'
} else {
  env.BUILD = 'CI'
}

/*
** Pipeline code.
*/
stage('Source') {
  node {
    checkoutCentreonBuild(buildBranch)
    dir('centreon-awie') {
      checkout scm
    }
    sh "./centreon-build/jobs/awie/${serie}/mon-awie-source.sh"
    source = readProperties file: 'source.properties'
    env.VERSION = "${source.VERSION}"
    env.RELEASE = "${source.RELEASE}"
    publishHTML([
      allowMissing: false,
      keepAll: true,
      reportDir: 'summary',
      reportFiles: 'index.html',
      reportName: 'Centreon AWIE Build Artifacts',
      reportTitles: ''
    ])
  }
}
try {
  stage('Unit tests // RPM Packaging // Sonar analysis') {
    parallel 'unit tests centos7': {
      node {
        checkoutCentreonBuild(buildBranch)
        /*
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-unittest.sh centos7"
        junit 'ut.xml'
        if (currentBuild.result == 'UNSTABLE')
          currentBuild.result = 'FAILURE'
        step([
          $class: 'CloverPublisher',
          cloverReportDir: '.',
          cloverReportFileName: 'coverage.xml'
        ])
        step([
          $class: 'hudson.plugins.checkstyle.CheckStylePublisher',
          pattern: 'codestyle.xml',
          usePreviousBuildAsReference: true,
          useDeltaValues: true,
          failedNewAll: '0'
        ])
        */

        // Run sonarQube analysis
        withSonarQubeEnv('SonarQubeDev') {
          sh "./centreon-build/jobs/awie/${serie}/mon-awie-analysis.sh"
        }
        timeout(time: 10, unit: 'MINUTES') {
          def qualityGate = waitForQualityGate()
          if (qualityGate.status != 'OK') {
            currentBuild.result = 'FAIL'
          }
        }
      }
    },
    'Packaging centos7': {
      node {
        checkoutCentreonBuild(buildBranch)
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-package.sh centos7"
        archiveArtifacts artifacts: 'rpms-centos7.tar.gz'
        stash name: "rpms-centos7", includes: 'output/noarch/*.rpm'
        sh 'rm -rf output'
      }
    },
    'Packaging alma8': {
      node {
        checkoutCentreonBuild()
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-package.sh alma8"
        archiveArtifacts artifacts: 'rpms-alma8.tar.gz'
        stash name: "rpms-alma8", includes: 'output/noarch/*.rpm'
        sh 'rm -rf output'
      }
    }
    if ((currentBuild.result ?: 'SUCCESS') != 'SUCCESS') {
      error('Unit tests stage failure.');
    }
  }

  stage('Delivery to unstable') {
    parallel 'centos7': {
      node {
        unstash 'rpms-alma8'
        unstash 'rpms-centos7'
        checkoutCentreonBuild()
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-delivery.sh"
      }
    }
    if ((currentBuild.result ?: 'SUCCESS') != 'SUCCESS') {
      error('Delivery stage failure.');
    }
  }

  stage('Docker creation') {
    parallel 'Docker centos7': {
      node {
        checkoutCentreonBuild()
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-bundle.sh centos7"
      }
    }
    'alma8': {
      node {
#        sh 'setup_centreon_build.sh'
        checkoutCentreonBuild(buildBranch)
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-bundle.sh alma8"
      }
    }
    if ((currentBuild.result ?: 'SUCCESS') != 'SUCCESS') {
      error('Bundle stage failure.');
    }
  }

  stage('Acceptance tests') {
    parallel 'centos7': {
      node {
#        sh 'setup_centreon_build.sh'
        checkoutCentreonBuild()
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-acceptance.sh centos7"
        junit 'xunit-reports/**/*.xml'
        if (currentBuild.result == 'UNSTABLE')
          currentBuild.result = 'FAILURE'
        archiveArtifacts allowEmptyArchive: true, artifacts: 'acceptance-logs/*.txt, acceptance-logs/*.png'
      }
    }
    'alma8': {
      node {
#        sh 'setup_centreon_build.sh'
        checkoutCentreonBuild()
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-acceptance.sh alma8"
        junit 'xunit-reports/**/*.xml'
        if (currentBuild.result == 'UNSTABLE')
          currentBuild.result = 'FAILURE'
        archiveArtifacts allowEmptyArchive: true, artifacts: 'acceptance-logs/*.txt, acceptance-logs/*.png'
      }
    }
    if ((currentBuild.result ?: 'SUCCESS') != 'SUCCESS') {
      error('Acceptance tests stage failure.');
    }
  }

  if ((env.BUILD == 'RELEASE') || (env.BUILD == 'REFERENCE')) {
    stage('Delivery') {
      node {
#        sh 'setup_centreon_build.sh'
        checkoutCentreonBuild()
	unstash 'rpms-centos7'
        unstash 'rpms-alma8'
        sh "./centreon-build/jobs/awie/${serie}/mon-awie-delivery.sh"
      }
      if ((currentBuild.result ?: 'SUCCESS') != 'SUCCESS') {
        error('Delivery stage failure.');
      }
    }
  }
} catch(e) {
  if ((env.BUILD == 'RELEASE') || (env.BUILD == 'REFERENCE')) {
    slackSend channel: "#monitoring-metrology", color: "#F30031", message: "*FAILURE*: `CENTREON AWIE` <${env.BUILD_URL}|build #${env.BUILD_NUMBER}> on branch ${env.BRANCH_NAME}\n*COMMIT*: <https://github.com/centreon/centreon-awie/commit/${source.COMMIT}|here> by ${source.COMMITTER}\n*INFO*: ${e}"
  }
  currentBuild.result = 'FAILURE'

